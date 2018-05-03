/**
 * URL 统一管理
 */
var URL_Manager = {
    login: "/login/in",
    item: "/item/item",
    items: '/item/items',
    parentDir: '/item/parent-dir',
    savefriend: '/item/save-item',
    rank: '/item/rank',
    //itemDraft
    itemDraft: "/item/item-draft",
    getnotes: '/note/item-notes',
    movenote: '/note/move-note',
    savenote: "/note/note",
    deletenote: "/note/note",
};

/**
 * 核心,不允许修改这些自有属性
 */
for (prop in URL_Manager) {
    if (URL_Manager.hasOwnProperty(prop)) {
        Object.defineProperty(URL_Manager, prop, {
            writable: false
        })
    }
}