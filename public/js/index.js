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
            el.focus();
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
            items: []
        },
        methods: {
            newNotes: function () {
                return {id: 0, item_id: this.item.id, content: "", c_time: now(), seen: true};
            },
            getNotes: function (item) {
                var my = this;
                my.item = item;
                my.items = ffz_items.items;
                $.ajax({
                    type: "POST",
                    url: URL_Manager.getnotes,
                    data: {item_id: item.id},
                    success: function (result) {
                        var data = result.data;
                        if (Array.isArray(data) && data.length === 0) {
                            if (!my.item.id) return false;
                            //如果没有，则添加一个默认的，具体带编辑
                            data = [my.newNotes()];
                        } else {
                            data = data.map(function (note) {
                                note.md = marked(note.content, {sanitize: true});
                                note.seen = false;
                                return note;
                            });
                        }
                        my.notes = data;
                    }
                });
            },
            add: function () {
                if (!this.item.id) return false;
                var note = this.newNotes();
                this.notes.unshift(note);
                return true;
            },
            edit: function (note) {
                note.seen = true;
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
                // 单击保存的时候，$event 为辅么未 undefine 呢？
                if (this.$refs['note'] === undefined) {
                    l('检查vue 2中添加的特殊属性 ref 是否发生变更');
                    return
                }
                var value = this.$refs['note'][0].value;
                if (!note.item_id) {
                    l('note' + note.id + '.item_id is 0');
                    return
                }
                // 将原来的及时更新改为非及时，以提高性能
                note.content = value;
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
                    }
                })
            },
            mv: function (note, index) {
                var my = this;
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.movenote,
                    data: {
                        id: note.id,
                        itemId: note.item_id
                    },
                    success: function (result) {
                        if (result.status) my.notes.splice(index, 1);
                    }
                })
            },

            /**
             * delete 是 javascript 的保留字
             */
            del: function (index) {
                var my = this;
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.deletenote,
                    data: {
                        id: this.notes[index].id
                    },
                    success: function (result) {
                        if (result.status) my.notes.splice(index, 1);
                    }
                })
            }
        }
    })

    ffz_items = new Vue({
        el: '#j_items',
        data: {fid: '', items: []},
        created: function () {
            this.fid = getParam('fid');
            this.getItems()
        },
        methods: {
            newItem: function () {
                return {id: 0, fid: this.fid, name: " ", rank: 0, seen: true}
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
                    type: 'POST',
                    url: URL_Manager.loaddata,
                    data: {fid: this.fid},
                    success: function (result) {
                        var data = result.data;
                        if (Array.isArray(data) && data.length === 0) {
                            data = [my.newItem()];
                            data.seen = true;
                        } else {
                            data = data.map(function (one) {
                                one.seen = false;
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
                    type: 'POST',
                    url: URL_Manager.savefriend,
                    data: {id: item.id, name: item.name, fid: item.fid},
                    success: function (result) {
                        if (!result.status) return;
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
                    type: 'POST',
                    url: URL_Manager.deletefriend,
                    data: {id: my.items[index].id},
                    success: function (result) {
                        if (!result.status)return;
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
                    type: 'POST',
                    url: URL_Manager.rank,
                    data: {
                        dragTo: item.id,
                        dragFrom: my.currentItem.id
                    },
                    success: function (result) {
                        if (result.status !== true) return;
                        my.currentItem.rank = item.rank - 1;
                        my.items.sort(function (a, b) {
                            a.rank = parseInt(a.rank);
                            b.rank = parseInt(b.rank);
                            return a.rank > b.rank ? -1 : ((b.rank > a.rank) ? 1 : 0);
                        });
                    }
                });

            },
            draft: function (item, index) {
                var my = this;
                // 表示checkBox 被选中,希望归档
                if (item.status !== true) return;
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.itemDraft,
                    data: {
                        id: item.id
                    },
                    success: function () {
                        if (my.items[index] === ffz_notes.item) {
                            ffz_notes.notes = []
                        }
                        my.items.splice(index, 1);
                    }
                })
            }
        }
    });
});

