var todoPanel = Vue.component('todo-lists-panel', {
	'prop': [],
	data: function () {
		return {
			collections: [],
			// 记录当前的item
			item: {},
			// 记录当前的items,页面下拉选择框需要用到
			items: []
		}
	},
	created: function () {
		var my = this;
		// 接受来自全局变量的 item items
		VGLOBAL.$on('should-get-todo-list', function (item) {
			console.log('should-get-todo-list')
			my.getList(item)
		});
	},
	methods: {
		getList: function (item) {
			var my = this
			if (typeof item == "undefined") {
				console.log('item is undefined', item)
				return;
			}
			$.ajax({
				type: "GET",
				url: URL_Manager.getTodoList,
				data: {
					item_id: item.id,
				},
				success: function (result) {
					var data = result.data;
					if (data.length === 0) {
						return false;
					}
					my.collections = data
				}
			});
		},
		todoDoneToday: function (collection, todo, tk) {
			var my = this
			// 通过 click.stop.prevent 阻止默认被勾选的行为
			$.ajax({
				type: "POST",
				url: URL_Manager.todoDoneToday,
				data: {
					todo_id: todo.id,
				},
				success: function (result) {
					if (result.status) {
						collection.todo.splice(tk, 1);
					}
				}
			});
		},
		collectionDoneToday: function (collection, ck) {
			var my = this
			// 通过 click.stop.prevent 阻止默认被勾选的行为
			$.ajax({
				type: "POST",
				url: URL_Manager.collectionDoneToday,
				data: {
					collection_id: collection.id,
				},
				success: function (result) {
					if (result.status) {
						my.collections.splice(ck, 1);
					}
				}
			});

		},
	},
	template: `
<div class="panel-heading">
    <div class="collection" v-for="(collection,ck) in collections"  :id="'collections_' + collection.id">
        <ul>
            <li class="collection_header">
                <span>
                    <input type="checkbox"  title="选中表示今天任务完成" @click.stop.prevent="collectionDoneToday(collection,ck)"/>
                </span>
                <span>共{{collection.total_count}}天,已打卡{{collection.check_in_count}}天,坚持率{{collection.check_in_count / collection.total_count * 100}}%</span> 
            </li>
            <li  v-for="(todo,tk) in collection.todo"  :id="'collections_' + collection.id">
                <div class="action-buttons">
                <span>
                    <input type="checkbox"  title="选中表示今天任务完成" @click.stop.prevent="todoDoneToday(collection,todo,tk)"/>
                </span>
                <span>{{todo.description}}</span>              
                </div>
            </li>
        </ul>
    </div>
</div>
`,
});