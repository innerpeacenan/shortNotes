function getParam(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

$(function () {
    /**
     * 加载头部信息
     * 月份从0开始计算，这一点与php和mysql一致
     */
    $(".countdown").text(Math.floor((new Date() - new Date(2016, 5, 2)) / (24 * 60 * 60 * 1000)));

    Vue.directive('focus', {
        inserted: function (el) {
            el.focus()
        }
    });
    Vue.directive('highlightjs', {
        inserted: function (el) {
            var blocks = el.querySelectorAll('pre code');
            Array.prototype.forEach.call(blocks, hljs.highlightBlock);
        },
        update: function (el, binding, vnode, oldVnode) {
            if (vnode.data.domProps.innerHTML !== oldVnode.data.domProps.innerHTML) {
                var blocks = el.querySelectorAll('pre code');
                Array.prototype.forEach.call(blocks, hljs.highlightBlock);
            }

        }
    });

    ffz_notes = new Vue({
        el: '#j_notes',
        data: {
            notes: [],
            items: [],
            // current item
            item: '',
        },
        computed: {
            // a computed getter
            showMore: function () {
                // `this` points to the vm instance
                // 当 limit 参数被设置未0的时候. 表征没有新的内容可以其请求了
                if (this.item) {
                    return this.item.limit
                } else {
                    // 页面刚加载进来的时候,不显示
                    return 0
                }
            }
        },
        methods: {
            newNotes: function () {
                return {id: 0, item_id: this.item.id, content: "", c_time: now(), seen: true, modifiedContent: ""};
            },
            getNotes: function (item) {
                var my = this;
                my.item = item;
                var oldLimit = my.item.limit
                my.item.limit += my.item.offset;
                my.item.offset = 0
                my.items = ffz_items.items
                this.doGetNotes(item, type = 'new')
                // 说明之前就没有新数据了
                if (0 === oldLimit) this.item.limit = 0
            },
            doGetNotes: function (item, type) {
                var my = this
                $.ajax({
                    type: "GET",
                    url: URL_Manager.getnotes,
                    data: {item_id: item.id, offset: item.offset, limit: item.limit},
                    success: function (result) {
                        var data = result.data;
                        if (Array.isArray(data) && data.length < my.item.limit) {
                            my.item.limit = 0
                        }
                        if (data.length === 0) {
                            if (!my.item.id) return false;
                            data = 'append' === type ? [] : [my.newNotes()]
                        } else {
                            data = data.map(function (note) {
                                note.md = marked(note.content, {sanitize: true});
                                note.seen = false;
                                return note;
                            });
                        }
                        my.notes = 'append' == type ? my.notes.concat(data) : data
                        my.item.offset = my.notes.length
                    }
                });
            },
            more: function () {
                // 保障 offset 整型,避免若类型的坑
                this.item.limit = 10
                this.item.offset = this.notes.length
                this.doGetNotes(this.item, type = 'append')
            },
            add: function () {
                if (!this.item.id) return false;
                var note = this.newNotes();
                this.notes.unshift(note);
                return true;
            },
            edit: function (note) {
                note.modifiedContent = note.content
                note.seen = true
            },
            /**
             * auto-height
             * @param $event
             */
            h: function ($event) {
                /**
                 * 这种方案预留了 50 个像素
                 */
                var target = $event.target;
                // height string such as: 50px,need to get substring 50
                var heightString = target.style.height;
                var height = heightString.substring(0, heightString.length - 2);
                if (height < target.scrollHeight) {
                    target.style.height = $event.target.scrollHeight + 50 + 'px';
                }
            },
            save: function (note) {
                var my = this
                // 单击保存的时候，$event 为辅么未 undefine 呢？
                if (!note.item_id) {
                    // l('note' + note.id + '.item_id is 0');
                    return
                }
                // 将原来的及时更新改为非及时，以提高性能
                note.content = note.modifiedContent;
                // l(note.modifiedContent)
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.savenote,
                    data: {id: note.id, item_id: note.item_id, content: note.content},
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

            /**
             * delete 是 javascript 的保留字
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
                            my.item.offset = my.notes.length
                        }
                    }
                })
            }
        }
    })

    ffz_items = new Vue({
        el: '#j_items',
        // define related consts

        data: {SHOW_GLOBAL: 1, ENABLE: 2, DRAFT: 3, fid: '', currentItem: '', items: []},
        created: function () {
            this.fid = getParam('fid')
            this.getItems()
        },
        methods: {
            newItem: function () {
                return {
                    id: 0,
                    fid: this.fid,
                    name: " ",
                    rank: 0,
                    status: this.ENABLE,
                    isChecked: 0,
                    seen: true,
                    offset: 0,
                    limit: 2,
                    // limit: 10,
                }
            },
            sort: function () {
                this.items.sort(function (a, b) {
                    // less(a,b) -1,a < b, 所有排序函数都是这种规律
                    if (parseInt(a.status) < parseInt(b.status)) {
                        // status asc
                        return -1
                    } else if (a.status == b.status) {
                        if (parseInt(a.rank) < parseInt(b.rank)) {
                            // order desc
                            return 1
                        } else if (a.rank == b.rank) {
                            return 0
                        } else {
                            return -1
                        }
                    } else {
                        return 1
                    }
                });
            },
            parentDir: function () {
                $.ajax({
                    data: {id: this.fid},
                    url: URL_Manager.parentDir,
                    success: function (result) {
                        if (result.status) {
                            var data = result.data;
                            ffz_items.fid = data.dir;
                            ffz_items.getItems()
                        }
                    }
                });
                ffz_notes.items = ffz_items.items
            },
            subDir: function (item) {
                ffz_items.fid = item.id;
                if (item.t_right - item.t_left <= 1) {
                    return
                }
                ffz_items.getItems()
            },
            getNotes: function (item) {
                ffz_notes.getNotes(item);
            },
            getItems: function () {
                var my = this;
                $.ajax({
                    type: 'GET',
                    url: URL_Manager.items,
                    data: {fid: this.fid},
                    success: function (result) {
                        var data = result.data;
                        if (Array.isArray(data) && data.length === 0) {
                            data = [my.newItem()];
                        } else {
                            data = data.map(function (one) {
                                one.seen = false;
                                // 初始化 page 参数
                                one.offset = 0;
                                // @todo 尽量不要应编码
                                one.limit = 10
                                // 全局显示的和启用的都勾选，这样更加美观
                                if (one.status == my.ENABLE || my.SHOW_GLOBAL) {
                                    one.isChecked = 0
                                } else {
                                    one.isChecked = 1
                                }
                                return one;
                            });
                        }
                        my.items = data;
                        ffz_notes.items = ffz_items.items;
                    }
                });
            },
            add: function () {
                this.items.unshift(this.newItem());
            }
            ,
            edit: function (item) {
                item.seen = !item.seen;
            },
            save: function (item) {
                $.ajax({
                    type: 'put',
                    url: URL_Manager.items,
                    data: {id: item.id, name: item.name, fid: item.fid},
                    success: function (result) {
                        // 针对插入的情况下,取出最后插入的主键
                        if (item.id == 0) {
                            item.id = result.data.id;
                        }
                        item.seen = !item.seen;
                    }
                })
            }
            ,
            del: function (index) {
                var my = this;
                $.ajax({
                    type: 'DELETE',
                    url: URL_Manager.item,
                    data: {id: my.items[index].id},
                    success: function (result) {
                        if (!result.status) return;
                        // 如果当前的笔记显示的是 要删除的item的，则清空相关比所有笔记
                        if (ffz_notes.item === my.items[index]) {
                            ffz_notes.notes = [];
                        }
                        my.items.splice(index, 1);
                    }
                })
            },
            drag: function (item) {
                this.currentItem = item;
            },
            drop: function (item) {
                var my = this;
                $.ajax({
                    type: 'PUT',
                    url: URL_Manager.rank,
                    data: {
                        dragTo: item.id,
                        dragFrom: my.currentItem.id
                    },
                    success: function (result) {
                        if (!result.status) return;
                        my.currentItem.rank = result.data.rank;
                        my.sort()
                    }
                });

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
                        if (!result.status) return;
                        var item = my.items[index];
                        item.status = result.data.status;
                        // 目前这种实现方案下，暂时不做处理了
                        // if (item === ffz_notes.item) {
                        //     ffz_notes.notes = []
                        // }
                        // my.items.splice(index, 1);
                        // 先这样吧，放到最底部
                        my.sort();
                    }
                })
            }
        }
    });
});

