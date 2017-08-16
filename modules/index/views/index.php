<!DOCTYPE html>
<html xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/index.css">
    <link href="http://cdn.bootcss.com/highlight.js/8.0/styles/monokai_sublime.min.css" rel="stylesheet">
    <script src="http://cdn.bootcss.com/highlight.js/8.0/highlight.min.js"></script>
    <script src="/js/md.js"></script>
    <script src="/js/jquery-2.0.2.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/vue-2-3.js"></script>
    <script src="/js/index.js"></script>
</head>
<body>
<style>

</style>

<header>
    <div id="header" class="row text-center">
        <div class="col-lg-12">
            <h3><span>make life easier</span>(第<span class="countdown"></span>天)</h3>
        </div>
    </div>
</header>
<div class="clearfix"></div>
<nav>
    <div><a class="col-sm-4 AppHeader-navItem">首页</a></div>
    <a class="col-sm-4 AppHeader-navItem ">命令行</a>
    <a class="col-sm-4 AppHeader-navItem">用户</a>
</nav>

<div class="clearfix"></div>
<div id="ffz_app">
    <div class="col-lg-4" id="j_items">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-list"></span>事项列表
                <span v-on:click.stop="parentDir">上一级目录</span>
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
                    <li class="list-group-item" v-on:click.stop="getNotes(item)" draggable='true'
                        @dragstart="drag(item)"
                        @dragover.prevent @drop="drop(item)">
                        <div class="checkbox">
                            <input type="checkbox" v-on:click.stop="toggleStatus(item,index)" v-model="item.isChecked" :disabled="item.status == 1"/>
                            <label for="checkbox">
                                <a style="display: inline-block" v-on:click.stop="subDir(item)"><span>{{item.id}}</span></a>
                                <span v-show="!item.seen">{{item.name}}</span>
                                <input v-model="item.name" v-on:click.stop="" v-show="item.seen"
                                       v-on:keyup.esc="save(item)">
                            </label>
                        </div>
                        <div class="pull-right action-buttons">
                            <a v-on:click.stop="add()"><span class="glyphicon glyphicon-plus-sign" title="添加新事项"></span></a>
                            <a v-on:click.stop="edit(item)"><span class="glyphicon glyphicon-pencil"
                                                                  title="编辑事项"></span></a>
                            <a v-on:click.stop="save(item)"><span class="glyphicon glyphicon-saved" title="保存事项"></span></a>
                            <a @click.stop="" @dblclick.stop="del(index)"><span class="glyphicon glyphicon-trash"
                                                                                title="删除事项"></span></a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="c_notes" class="panel-heading">
        <ul id="j_notes" class="col-lg-8">
            <li v-for="(note,index) in notes" :id="'note_' + note.id">
                <span>{{note.id}}</span>
                <span class="hidden-xs">{{note.c_time}}</span>
                <span>
                    <select v-model="note.item_id" @change="mv(note,index)">
                        <template v-if="note.item_id == item.id">
                        <option v-for="item in items" :value="item.id" selected>{{item.name}}</option>
                        </template>
                        <template v-else>
                        <option v-for="item in items" :value="item.id">{{item.name}}</option>
                        </template>
                </select>
                </span>
                <div class="pull-right action-buttons">
                    <span v-on:click.stop="add()">
                        <a class="glyphicon glyphicon-plus-sign" title="添加笔记">
                        </a>
                    </span>
                    &nbsp;
                    <span v-on:click.stop="edit(note, $event)">
                        <a class="glyphicon glyphicon-edit" title="编辑笔记">
                        </a>
                    </span>
                    &nbsp;
                    <span v-on:click.stop="save(note)">
                        <a class="glyphicon glyphicon-saved" title="保存笔记">
                        </a>
                    </span>
                    &nbsp;&nbsp;
                    <span v-on:click.stop="" v-on:dblclick="del(index)">
                        <a class="glyphicon glyphicon-trash" title="删除笔记"></a>
                    </span>
                </div>
                <div>
                    <textarea class="col-xs-12" ref="note" v-if="note.seen" :value="note.content"
                              v-on:keyup.esc="save(note, $event)"
                              v-on:keyup.enter="h($event)" @focus="h($event, note)" @paste="h($event, note)"
                              v-focus></textarea>
                    <div class="textarea" v-if="!note.seen" @dblclick.stop="edit(note)"
                         v-html="note.md" v-highlightjs></div>
                </div>
            </li>
        </ul>
    </div>
</div>


<!--用来清除浮动, 针对pull-right-->
<div class="clearfix"></div>
<!--footer-->
<footer class="row">
    <p class="text-center">
        <i>2016 All rights reserved</i><i>nxn copyright</i>
    </p>
</footer>

<!------------------------------------------------js------------------------------------------------------------------------- --->

<script>
    /**
     * URL 统一管理
     * @todo 里边的部分地址需要剔除
     */
    URL_Manager = {
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

</script>

</body>
</html>
