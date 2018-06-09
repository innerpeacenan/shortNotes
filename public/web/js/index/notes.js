var notesPanel = Vue.component('notes-panel', {
	'prop': [],
	data: function () {
		return {
			settings: {
				// 单位是像素
				textarea_default_height: 50
			},
			notes: [],
			// 记录当前的item
			item: {},
			// 记录当前的items,页面下拉选择框需要用到
			items: []
		}
	},
	computed: {
		// a computed getter
		showMore: function () {
			// 当 limit 参数被设置未0的时候. 表征没有新的内容可以其请求了
			if (this.item) {
				return this.item.limit
			} else {
				// 页面刚加载进来的时候,不显示
				return 0
			}
		}
	},
	created: function () {
		var my = this;
		// 接受来自全局变量的 item items
		VGLOBAL.$on('should-get-notes', function (item, items) {
			console.log('should-get-notes')
			my.item = item;
			my.items = items;
			my.doGetNotes(item)
		});

		VGLOBAL.$on('items-change', function (items) {
			console.log('item-change', 'my.item', my.item)
			my.items = items;
		});

		// 如果当前的笔记显示的是要删除的item的，则清空相关所有笔记
		VGLOBAL.$on('item-delete', function (item) {
			console.log('item-delete', 'item', item, 'my.item', my.item)
			if (item === my.item) {
				my.notes = [];
			}
		});
	},
	methods: {
		newNote: function () {
			if (typeof this.item == 'undefined') {
				window.console.log.error('this.items is undefined')
				var itemId = 0;
			} else {
				var itemId = this.item.id;
			}
			return {
				id: 0,
				item_id: itemId,
				content: "",
				c_time: '',
				seen: true,
				modifiedContent: ""
			};
		},
		doGetNotes: function (item, type) {
			var my = this
			if (typeof item == "undefined") {
				console.log('item is undefined', item)
				return;
			}
			$.ajax({
				type: "GET",
				url: URL_Manager.getnotes,
				data: {
					item_id: item.id,
					offset: item.offset,
					limit: item.limit
				},
				success: function (result) {
					var data = result.data;

					if (Array.isArray(data) && data.length < item.limit) {
						// 标志着再没有更多的笔记需要加载了
						item.limit = 0
					}

					if (data.length === 0) {
						if (!item.id) {
							return false;
						}
						// 设置是否为追加模式,如果是发展为模式,返回控列表,否则返回新的一条记录
						// 主要处理第一次加载的时候的问题
						data = 'append' === type ? [] : [my.newNote()]
					} else {
						data = data.map(function (note) {
							note.md = marked(note.content, {sanitize: true});
							note.seen = false;
							return note;
						});
					}
					my.notes = 'append' == type ? my.notes.concat(data) : data
					item.offset = my.notes.length
				}
			});
		},
		add: function () {
			if ((typeof this.item == 'undefined') || (!this.item.id)) {
				return false;
			}
			var note = this.newNote();
			// 在列表头部加一条数据
			this.notes.unshift(note);
			return true;
		},
		/**
		 * auto-height 在编辑的时候,自动调整 textarea 高度
		 * @param $event
		 */
		h: function ($event) {
			/**
			 * 这种方案 50px 扩充一次高度
			 */
			var target = $event.target;
			// height string such as: 50px,need to get substring '50' from '50px'
			var heightString = target.style.height;
			var height = heightString.substring(0, heightString.length - 2);
			if (height < target.scrollHeight) {
				target.style.height = $event.target.scrollHeight + this.settings.textarea_default_height + 'px';
			}
		},
		edit: function (note) {
			note.modifiedContent = note.content
			note.seen = true
		},
		save: function (note) {
			var my = this
			// 单击保存的时候，$event 为什么是 undefine 呢？
			if (!note.item_id) {
				console.log('note is:', note)
				return
			}
			// 将原来的及时更新改为非及时，以提高性能
			note.content = note.modifiedContent;
			$.ajax({
				type: 'POST',
				url: URL_Manager.savenote,
				data: {
					id: note.id,
					item_id: note.item_id,
					content: note.content
				},
				success: function (result) {
					// 为新增加 note 更新其对应 id 值
					if (result.status) {
						if (0 == note.id) {
							note.id = result.data.id
						}
						// senitize 对 html 标签用实体替换，尽量避免跨站点脚本攻击
						note.md = marked(note.content, {sanitize: true});
					}
					// 不管有没有实际更新数据,都自动保存数据
					note.seen = false
					my.item.offset = my.notes.length
				}
			})
		},
		/**
		 * delete 是 javascript 的保留字
		 * 删除笔记
		 */
		del: function (index) {
			var my = this;
			$.ajax({
				type: 'DELETE',
				url: URL_Manager.deletenote,
				data: {
					id: this.notes[index].id
				},
				success: function (result) {
					if (result.status) {
						my.notes.splice(index, 1);
						// 每次重新设置请求的起点,保证所请求的数据能够连接起来
						my.item.offset = my.notes.length
					}
				}
			})
		},
		mv: function (note, index) {
			var my = this;
			$.ajax({
				type: 'PUT',
				url: URL_Manager.movenote,
				data: {
					id: note.id,
					itemId: note.item_id
				},
				success: function (result) {
					if (result.status) {
						my.notes.splice(index, 1);
						my.item.offset = my.notes.length
					}
				}
			})
		},
		//获取更多数据
		more: function () {
			if (!this.item) {
				return;
			}
			VGLOBAL.$emit('item-update-limit-and-offset', this.item);
			this.doGetNotes(this.item, type = 'append')
		},
		done: function (note, index) {
			var my = this;
			$.ajax({
				type: 'PUT',
				url: URL_Manager.noteDone,
				data: {
					note_id: note.id,
				},
				success: function (result) {
					if (result.status) {
						my.notes.splice(index, 1);
						// 目前移除掉完成的
						my.item.offset = my.notes.length;
					}
				}
			})
		},
	},

	template: `
<div class="panel-heading">
    <div>
        <ul>
            <li v-for="(note,index) in notes"  :id="'note_' + note.id">
                <span title="笔记唯一编号">{{note.id}}</span>
                <span class="hidden-xs" title="创建时间">{{note.c_time}}</span>
                <span>
                    <select title="选择其他目录,则自动将笔记移动到其他目录下了" class="items" v-model="note.item_id" @change="mv(note,index)" >
                        <template v-if="note.item_id == item.id">
                            <option v-for="item in items" :value="item.id" selected>{{item.name}}</option>
                            </template>
                            <template v-else>
                            <option v-for="item in items" :value="item.id">{{item.name}}</option>
                        </template>
                    </select>
                </span>
                <div class="pull-right action-buttons">
                <span @click.stop="done(note, index)">
                    <input type="checkbox"  title="标记完成" v-model="note.isChecked" />
                </span>
                <span @click.stop="add()">
                    <a class="glyphicon glyphicon-plus-sign" title="添加笔记">
                    </a>
                </span>
                    &nbsp;
                    <span @click.stop="edit(note, $event)">
                    <a class="glyphicon glyphicon-edit" title="编辑笔记">
                    </a>
                </span>
                    &nbsp;
                    <span @click.stop="save(note)">
                    <a class="glyphicon glyphicon-saved" title="保存笔记">
                    </a>
                </span>
                    &nbsp;&nbsp;
                    <span @click.stop="" @dblclick="del(index)">
                    <a class="glyphicon glyphicon-trash" title="双击删除笔记"></a>
                </span>
                </div>
                <div>
                <textarea class="col-xs-12" v-if="note.seen" v-model="note.modifiedContent"
                          @keyup.esc="save(note)"
                          @keyup.enter="h($event)" @focus="h($event, note)" @paste="h($event, note)"
                          v-focus>
                </textarea>
                    <div class="textarea" v-if="!note.seen" @dblclick.stop="edit(note)"
                         v-html="note.md" v-highlightjs></div>
                </div>
            </li>
        </ul>
        <div class="text-center more" @click.stop="more()" v-show="showMore">更多</div>
    </div>
</div>
`,
});

new Vue({
	el: '#ffz_app',
	data: {
		seen_items: 1,
	},
	computed: {
		// 不设置该值, 会造成 checkbox 保留上次被操作的状态,这不是我想要的
		isChecked: function () {
			if (this.seen) {
				return 0;
			} else {
				return 1;
			}
		}
	},
	methods: {
		toggleItemsTab: function () {
			console.log('toggleItemsTab trigger')
			this.seen_items = this.seen_items == 1 ? 0 : 1;
		}
		// 子组件变化通知到父组件, 父组件变化在通知到另外一个子组件
	},
	components: {
		'items-panel': itemsPanel,
		'notes-panel': notesPanel,
	}
});