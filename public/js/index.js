
function getParam(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// 根据情况使用
String.prototype.lines = function () {
    return this.split(/\r*\n/);
};
String.prototype.lineCount = function () {
    return this.lines().length;
};

// Register a global custom directive called v-focus
Vue.directive('focus', {
    // When the bound element is inserted into the DOM...
    inserted: function (el) {
        // Focus the element
        el.focus()
    }
})


Vue.directive('highlightjs', function(el) {
    var blocks = el.querySelectorAll('pre code');
    Array.prototype.forEach.call(blocks, hljs.highlightBlock);
})


$(function () {
    /**
     * notes 对象,代表和管理着一个item下的所有note
     */
    window.ffz_notes = new Vue({
        el: '#j_notes',
        data: {
            notes: [],
            _autoHeight: false,
            items: [],
            item: null
        },
        methods: {
            compiledMarkdown: function (note) {
                return marked(note.content, { sanitize: true });
            },
            mv: function (note, index) {
                var my = this;
                // 搞定
                // l(note);
                $.ajax({
                    url: URL_Manager.movenote,
                    type : 'POST',
                    data: {
                        id: note.id,
                        itemId: note.item_id
                    },
                    success: function (result) {
                        if (result.status) {
                            l(my.notes);
                            my.notes.splice(index, 1);
                        }
                    }
                })
            },
            getNotes: function (item) {
                /**
                 * 将this对象的引用传递给一个变量,可以较好避免this带来的歧义
                 */
                var my = this;
                my.item = item;
                my.items = ffz_items.items;
                $.ajax({
                    type: "POST",
                    url: URL_Manager.getnotes,
                    data: {
                        item_id: item.id
                    },
                    success: function (result) {
                        var data = result.data;
                        /**
                         * 如果没有，则添加一个默认的，具体带编辑
                         */
                        if (Array.isArray(data) && data.length === 0) {
                            if (my.item.id === undefined) {
                                return false;
                            }
                            data = [{id: 0, item_id: my.item.id, content: "", c_time: now(), seen: true}];
                        }
                        data = data.map(function (note) {
                            note['seen'] = false;
                            return note;
                        });
                        my.notes = data;
                    }
                });
            },
            /**
             *
             * @param $event
             * @param note
             */
            h: function ($event, note) {
                /**
                 * 这种方案预留了 50 个像素
                 */
                var target = $event.target;
                var heightString = target.style.height;
                var height = heightString.substring(0, heightString.length - 2);
                if (height < target.scrollHeight) {
                    //scroll Height is read only
                    target.style.height = $event.target.scrollHeight + 50 + 'px';
                }
            },
            /**
             * 通过 $event.target 直接操作dom,解决了之前存在的 dom 未准备好的问题
             * 但是这种方式并不是绑定数据驱动的,在数据销毁的时候,对应的dom节点的属性还在
             * 因此,采用计算文本行数的方式(开销稍微大点,但是在能接受的范围内)
             * @param $event
             * @param note
             */
            h1: function ($event, note) {
                $event.target.style.height = note.content.lineCount() + 5 + "em";
            },
            /**
             * @deprecated
             * 之前是由于返回的data未对象格式,未遍历带来了一定深度的不便,因此,再改才采用这种方式遍历
             */
            removeItem: function (index) {
                var notes = [];
                for (var i in this.notes) {
                    if (this.notes.hasOwnProperty(i)) {
                        if (i !== index) {
                            notes.push(this.notes[i]);
                        }
                    }
                }
                this.notes = notes;
            },
            edit: function (note) {
                note.seen = true;
            },
            add: function () {
                /**
                 * Cannot read property 'id' of null
                 */
                if (this.item.id === undefined) {
                    l(this.item);
                    return false;
                }
                var note = {id: 0, item_id: this.item.id, content: "", c_time: now(), seen: true};
                this.notes.unshift(note);
                return true;
            },
            /**
             * delete 是 javascript 的保留字
             */
            del: function (index) {
                that = this;
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.deletenote,
                    data: {
                        // notes is not defined whihout using this
                        id: this.notes[index].id
                    },
                    success: function (result) {
                        /**
                         * @done http respon header 部分加了 "Content-Type:application/json"后,返回的之间未json对象
                         * Unexpected token u in JSON at position 0
                         * JSON.parse() 方法没有给参数的情况下,会报这个错误
                         */
                        if (result.status) that.notes.splice(index, 1);
                    }
                })
            }
            ,
            save: function (note) {
                if (undefined === note.item_id || 0 === note.item_id) {
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.savenote,
                    data: {
                        id: note.id,
                        item_id: note.item_id,
                        content: note.content
                    },
                    success: function (result) {
                        // 为增加 note 添加的方法
                        if (result.data && 0 == note.id) note.id = result.data;
                        if (result.status) note.seen = false;
                    }
                })
            }
        }
    });

//[获取所绑定的事件的元素](https://github.com/vuejs/vue/issues/1181)
    window.ffz_items = new Vue({
        el: '#j_items',
        data: {
            items: [],
            currentItem: {},
            // rank 排序号码
            newItem: {id: 0, fid: getParam('fid'), name: "", rank: 0, seen: true}
        },
        methods: {
            getNotes: function (item) {
                window.ffz_notes.getNotes(item);
            },
            /**
             * 获取所有 items
             */
            getItems: function () {
                var my = this;
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.loaddata,
                    data: {
                        fid: getParam('fid')
                    },
                    success: function (result) {
                        var data = result.data;
                        if (Array.isArray(data) && data.length === 0) {
                            // @todo new a item
                            data = [my.newItem];
                        }
                        data = data.map(function (one) {
                            one['seen'] = false;
                            return one;
                        })
// change its property directory, it can work better with jquery
                        my.items = data;
                        // l(my.items);
                    }
                });
            }
            ,
            /**
             * this.newItem because my is undefined right now (used to use my.undefined)
             * @todo bug: Cannot read property 'id' of undefined, when adding new note,  this error happened
             */
            add: function () {
                l(this.newItem);
                // @todo is it valid ? deep clone an item
                this.items.unshift(JSON.parse(JSON.stringify(this.newItem)));
            }
            ,
            save: function (item) {
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.savefriend,
                    data: {
                        id: item.id,
                        name: item.name,
                        fid: item.fid
                    },
                    success: function (result) {
                        l(result);
                        // 针对插入的情况下,取出最后插入的主键
                        if (result.data && item.id == 0)  item.id = result.data;
                        if (result.status) item.seen = !item.seen;
                    }
                })
            }
            ,
            del: function (index) {
                var my = this;
                $.ajax({
                    type: 'POST',
                    url: URL_Manager.deletefriend,
                    data: {
                        /**
                         * Cannot read property 'id' of undefined
                         */
                        id: my.items[index].id
                    },
                    success: function (result) {
                        if (result.status) my.items.splice(index, 1);
                        // 将笔记置空
                        ffz_notes.notes = [];
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
                        if (result.status === true) {
                            // 修改排序号码(后台将item 的 rank -1 赋值该 currentItem)
                            // fiexd varable data is undefined, use result instand
                            my.currentItem.rank = item.rank - 1;
                            // 降序排列 a=parseInt(a.rank); @todo 之前把 a 的数据类型都整坏了
                            my.items.sort(function (a, b) {
                                a.rank = parseInt(a.rank);
                                b.rank = parseInt(b.rank);
                                return a.rank > b.rank ? -1 : ((b.rank > a.rank) ? 1 : 0);
                            });
                        } else {
                            console.log('error');
                        }

                    }
                });

            },
            draft: function (item, index) {
                var my = this;
                // 表示checkBox 被选中,希望归档
                if (item.status === true) {
                    $.ajax({
                        type: 'POST',
                        url: URL_Manager.itemDraft,
                        data: {
                            id: item.id
                        },
                        success: function ($result) {
                            // @todo 暂不处理
                            my.items.splice(index, 1);
                            ffz_notes.notes = [];
                        }
                    })
                }
            }

        }
    });
})

$(function () {

    /**
     * 加载头部信息
     * 月份从0开始计算，这一点与php和mysql一致
     */
    $(".countdown").text(Math.floor((new Date() - new Date(2016, 5, 2)) / (24 * 60 * 60 * 1000)));
    // get all items
    window.ffz_items.getItems();

})

// forcing readjustments on viewport resize
// $(window).resize(function () {
//     $('textarea').autoTextarea();
// });

