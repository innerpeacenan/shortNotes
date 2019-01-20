var itemsPanel = Vue.component('items-panel', {
	data: function () {
		return {
			settings: {
				perpage: 10 //设置首次加载个数

			},
			// 项目用到的常量都放到这里
			constant: {
				visible_range: {
					show_global: {
						code: 20,
						desc: "全局显示"
					},
					show_inside_parent: {
						code: 10,
						desc: "只在父items下可见"
					}
				},
				status: {
					enable: {
						code: 10,
						desc: "启用"
					},
					staging: {
						code: 20,
						desc: "暂存"
					},
					draft: {
						code: 30,
						desc: "归档"
					}
				}
			},
			runtimeState: {
				fid: '',
				drag_from: {}, // 拖动的item
				drag_to: {},  // 要拖动到的item
			},
			currentItem: '',
			// 所拥有的items集合
			items: []
		}
	},
	prop: [],
	// 组件初始化的钩子, 不能放到普通的 methods 中去
	created: function () {
		this.runtimeState.fid = getParam('fid');
    window.console.log(this.runtimeState)
		this.getItems();
		if (typeof VGLOBAL == 'undefined') {
			return;
		}
		// 监听重置分页选项事件
		VGLOBAL.$on('item-reset-limit-and-offset', function (item) {
			// 保障 offset 整型,避免弱类型的坑
			this.item.limit = this.settings.perpage;
			this.item.offset = this.notes.length
		});
	},
	methods: {
		// 新建一个数据model
		newItem: function () {
			return {
				id: 0,
				fid: this.runtimeState.fid,
				name: " ",// make it not empty, so it can be saved where click save button directively
				rank: 0,
				status: this.constant.status.enable.code,
				visible_range: this.constant.visible_range.show_inside_parent.code,
				// above are extra property
				isChecked: 0,
				seen: true, // make it editable, so you could see the textarea
				// 设置每个items在请求notes的时候设置的分页参数
				limit: this.settings.perpage,
				offset: 0,
			}
		},
		getItems: function () {
			var my = this;
			var fid = my.runtimeState.fid;
			$.ajax({
				type: 'GET',
				url: URL_Manager.items,
				data: {
					fid: fid,
					status: [
						my.constant.status.enable.code,
					],
				},
				success: function (result) {
					var data = result.data;
					if (Array.isArray(data) && data.length === 0) {
						data = [my.newItem()];
					} else {
						data = data.map(function (one) {
							one.seen = false;
							// 初始化 page 参数
							one.offset = 0;
							one.limit = my.settings.perpage;
							one.isChecked = my.constant.status.staging.code == one.status ? 1 : 0;
							return one;
						});
					}
					my.items = data;
					// 通知者
					VGLOBAL.$emit('items-change', my.items);
				},
				error: function (data) {
					console.log(data)
				}
			});
		},
		// 当点击上一级目录的时候触发
		parentDir: function () {
			var my = this;
			var id = this.runtimeState.fid;
			$.ajax({
				data: {id: id},
				url: URL_Manager.parentDir,
				success: function (result) {
					if (result.status) {
						var data = result.data;
						// 记录当前item的父ID
						my.runtimeState.fid = data.dir;
						// 更新相应的items列表
						my.getItems()
					}
				}
			});
		},
		// 当点即一个items的时候, 进入一个items的子items
		subDir: function (item) {
			var that = this;
			// 记录当前items所在的父目录
			that.runtimeState.fid = item.id;
      window.console.log('that.runtimeState', that.runtimeState)
			// 请求该父目录下的所有itmes
			that.getItems();
		},
		// 单击添加按钮的时候
		add: function () {
			this.items.unshift(this.newItem());
		},
		// 单击编辑按钮的时候
		edit: function (item) {
			item.seen = !item.seen;
		},
		save: function (item) {
			var my = this;
			$.ajax({
				type: 'put',
				url: URL_Manager.item,
				data: {
                  id: item.id, 
                  name: item.name, 
                 fid: my.runtimeState.fid
        },
				success: function (result) {
					// 针对插入的情况下,取出最后插入的主键
          var needSort = false
					if (item.id == 0) {
            needSort = true 
						item.id = result.data.id
            item.rank = result.data.rank
					}
					item.seen = !item.seen;
          if(needSort){
              my.sort()
          }
				}
			})
		},
		// 删除 items
		del: function (index) {
			var my = this;
			var item = my.items[index];
			$.ajax({
				type: 'DELETE',
				url: URL_Manager.item,
				data: {id: my.items[index].id},
				success: function (result) {
					if (!result.status) {
						console.log('delete error', index, result)
						return;
					}
					// 通知笔记组件检查当前所属的事项是否被删除,如果被删除,也应该在页面上移除所有的笔记
					// 后台也是真实删除了笔记的,目前没有删除笔记对应的tag,不影响使用
					// 所以删除和归档不同,要慎重,与其他按钮单击事件不同,删除必须采用单击事件
					VGLOBAL.$emit('item-delete', item)
					my.items.splice(index, 1);
				}
			})
		},
		// 拖拽实践, 用来 items 排序
		drag: function (item) {
			this.runtimeState.drag_from = item;
			console.log('drag:', this.runtimeState, 'drag_end');
		},
		// 确定 rankFrom.rank 将要被修改的值, 并重新排序
		drop: function (item) {
			var my = this;
			var dragFrom = my.runtimeState.drag_from;
			var dragTo = my.runtimeState.drag_to = item;
			console.log('drop', dragFrom, dragTo, 'end')
			var rankVal = 0;
			var toIndex = my.items.indexOf(item);
			if (parseInt(dragFrom.visible_range != dragTo.visible_range)) {
				console.log('visible_range are the same', dragFrom.visible_range, dragTo.visible_range)
			}

			// 默认排序是降序的,因此判断是从下往上拖动了
			if (parseFloat(dragFrom.rank) < parseFloat(dragTo.rank)) {
				toIndex = my.items.indexOf(dragTo);
				// 最终要将元素移动到 prevIndex 和 toIndex 之间
				if (toIndex - 1 < 0) {
					// fix bug about rank error
					rankVal = parseFloat(dragTo.rank) + 1 / 3;
					console.log('case 1:', dragFrom.rank, dragTo.rank, rankVal);
				} else {
					var toPrev = my.items[toIndex - 1];// 移动目标item的前一个item
					rankVal = (parseFloat(toPrev.rank) + parseFloat(dragTo.rank)) / 2;
					console.log('case 2:', dragFrom.rank, dragTo.rank, toPrev.rank, rankVal);
				}
			}
			// 从上往下移动
			else if (parseFloat(dragFrom.rank) > parseFloat(dragTo.rank)) {// 55->8
				if (toIndex + 1 >= my.items.length) {
					rankVal = parseFloat(dragTo.rank) - 1 / 3;
					console.log('case 3:', dragFrom.rank, dragTo.rank, rankVal);
				} else {
					var toNext = my.items[toIndex + 1];
					rankVal = (parseFloat(dragTo.rank) + parseFloat(toNext.rank)) / 2;
					console.log('case 4:', dragFrom.rank, dragTo.rank, toNext.rank, rankVal);
				}
			} else {
				console.log('case 5:', dragFrom.rank, dragTo.rank)
			}
			// save rank
			$.ajax({
				type: 'PUT',
				url: URL_Manager.rank,
				data: {
					"dragFrom": dragFrom.id,
					"dragTo": dragTo.id,
					"rank": rankVal
				},
				success: function (result) {
					if (!result.status) {
						console.log('rank error', result)
						return;
					} else {
						dragFrom.rank = result.data.rank;
					}
					my.sort()
				}
			});
		},
		sort: function () {
			this.items.sort(function (a, b) {
				// 返回值大于0 表示需要调整顺序
				if (parseInt(a.visible_range) > parseInt(b.visible_range)) {
					return -1;
				} else if (a.visible_range < b.visible_range) {
					return 1;
				} else {
					// 降序排列
					if (parseFloat(a.rank) > parseFloat(b.rank)) {
						return -1;
					} else if (parseFloat(a.rank) < parseFloat(b.rank)) {
						return 1;
					} else {
						return 0;
					}
				}
			});
		},
		getDetails: function (item) {
			var my = this;
			my.getNotes(item);
			my.getTodoList(item);
		},
		getTodoList: function (item) {
			VGLOBAL.$emit('should-get-todo-list', item)
		},
		getNotes: function (item) {
			var my = this;
			// 完成 items 和 notes 的相互绑定
			item.offset = 0;
			item.limit = my.settings.perpage;
			VGLOBAL.$emit('should-get-notes', item, my.items)
		},
		toggleStatus: function (item, index) {
			var my = this;
			$.ajax({
				type: 'PUT',
				url: URL_Manager.itemDraft,
				data: {
					id: item.id
				},
				success: function (result) {
					if (!result.status) {
						console.log('items send to draft failed!', item)
						return;
					}
					my.items.splice(index, 1)
					VGLOBAL.$emit('item-delete', item)
				}
			})
		},
		toggleVisibleRange: function (item) {
			var my = this;
			$.ajax({
				type: 'PUT',
				url: URL_Manager.itemToggleVisibleRange,
				data: {
					id: item.id,
				},
				success: function (result) {
					console.log(result);
					if (result.status) {
						item.visible_range = result.data.visible_range;
						// 重新调整顺序
						my.sort();
					}
				}
			})
		}
	},
	template: `
<div>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-list"></span>事项列表
            <span @click.stop="parentDir">上一级目录</span>
            <div class="pull-right action-buttons">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-cog" style="margin-right: 0px;"></span>
                    </button>
                    <ul class="dropdown-menu slidedown">
                        <li><a href=""><span class="glyphicon glyphicon-pencil"></span>Edit</a></li>
                        <li><a href=""><span class="glyphicon glyphicon-trash"></span>Delete</a></li>
                        <li><a href=""><span class="glyphicon glyphicon-flag"></span>Flag</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <ul class="list-group" v-for="(item,index) in items">
                <li class="list-group-item" @click.stop="getDetails(item)" draggable='true'
                    @dragstart="drag(item)"
                    @dragover.prevent @drop="drop(item)">
                    <div class="checkbox">
                        <input type="checkbox" @click.stop="toggleStatus(item,index)" v-model="item.isChecked"
                               :disabled="item.status == 1"/>
                        <label for="checkbox">
                            <a style="display: inline-block" @click.stop="subDir(item)" title="事项或目录ID,单击可进入目录"><span>{{item.id}}</span></a>

                            <span v-show="!item.seen" title = "事项名称" >{{item.name}}</span>
                            <input v-model="item.name" @click.stop="" v-show="item.seen"
                                   @keyup.esc="save(item)"/>
                        </label>
                    </div>
                    <div class="pull-right action-buttons">
                        <a  v-bind:title = "item.visible_range == constant.visible_range.show_global.code ? '全局可见,单击可改变可见范围' : '仅当前目录下可见,双击可改变可见范围'"
                            v-bind:class="item.visible_range == constant.visible_range.show_global.code ? 'glyphicon glyphicon-globe' : 'glyphicon glyphicon-globe gray'"
                           @dblclick.stop="toggleVisibleRange(item)"></a>
                        <a @click.stop="add()"><span class="glyphicon glyphicon-plus-sign" title="添加新事项"></span></a>
                        <a @click.stop="edit(item)"><span class="glyphicon glyphicon-pencil"
                                                              title="编辑事项"></span></a>
                        <a @click.stop="save(item)"><span class="glyphicon glyphicon-saved" title="保存事项"></span></a>
                        <a @click.stop="" @dblclick.stop="del(index)"><span class="glyphicon glyphicon-trash"
                                                                            title="双击删除事项(请慎重操作)"></span></a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
`,
});
