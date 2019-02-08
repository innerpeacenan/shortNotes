/**
 * URL 统一管理
 */
var URL_Manager = {
	login: "/login/in",
	item: "/item/item",
	items: '/item/items',
	itemToggleVisibleRange: 'item/toggle-visible-range',
	parentDir: '/item/parent-dir',
	savefriend: '/item/save-item',
	rank: '/item/rank',
	getTodoList: "/item/todo-list",
	todoDoneToday: "/item/todo-done-today",
	collectionDoneToday: "/item/collection-done-today",
	//itemDraft
	itemDraft: "/item/item-draft",
	getnotes: '/note/item-notes',
	moveNote: '/note/move-note',
	saveNote: "/note/note",
	deleteNote: "/note/note",
	noteDone: "/note/note-done",
	image:'image/base64',
};

/**
 * 不允许修改这些自有属性
 */
for (prop in URL_Manager) {
	if (URL_Manager.hasOwnProperty(prop)) {
		Object.defineProperty(URL_Manager, prop, {
			writable: false
		})
	}
}