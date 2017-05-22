<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/index.css">
<!--    <link rel="stylesheet" href="/highlight/styles/rainbow.css">-->
    <link href="http://cdn.bootcss.com/highlight.js/8.0/styles/monokai_sublime.min.css" rel="stylesheet">
    <script src="http://cdn.bootcss.com/highlight.js/8.0/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <!--<link href="/css/mricode.pagination.css" rel="stylesheet" />-->
<!--    <script src="https://cdn.bootcss.com/marked/0.3.6/marked.js"></script>-->
    <script src="/js/md.js"></script>
    <script src="/js/jquery-2.0.2.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/loadash.js"></script>
    <script src="/js/vue-2-3.js"></script>
    <script src="/js/index.js"></script>

</head>
<body>

<!-- @done 数据绑定特别爽,醉主要尽然可以和 jquery 混合使用,特别爽,明天完成编辑,保存和删除三项, -->
<!--@done 重新设计数据库模式,修改现有的 JSON 交互格式 -->
<!--@done 添加一个新的 note -->
<!--    @todo 整体调整样式-->
<!--@todo 加入早期设计的text-area auto-height得特征，并弄清楚其原理-->
<!--@todo 表结构，加入排序号码，tem 设计未无限级属性结构 ->
<!--@todo 写一套登录系统,研究 session, cookie 管理, 验证码的实现机制, 邮箱实现机制和通信原理等内容, 这一块的工作量其实还是蛮大的-->
<!--@todo 写一套后端简单的 mvc 路由机制，脱离对 zend frame work 这个庞大的框架的依赖 -->
<!--@todo 重新设计表表结构(i),通过url后后面部分,在得到Module,controller和action信息后,将其余部分设计为`current working directory`,从而实现用数据库数结构模拟文件结构->
<!--@todo 设计一个命令输入框,通过该输入框,可以作一些更灵活的工作,具体的需求还需要进一步明确（rm,mv,cp,link,touch etc）,同时加计算机网络安全方面的知识，预防安全问题-->
<!--@todo 添加前后台的数据过滤-->

<!-- @todo 采用这种方法可以跳转到页面指定的位置，但是，这是什么原理呢，为什么没有包括url部分呢？window.location.href="#note_22" -->
<!--<script src="http://miaolz123.github.io/vue-markdown/dist/vue-markdown.js"></script>-->

<header>
    <div id="header" class="row text-center">
        <input class="col-lg-12" placeholder="command line,support mv,cp,ls,etc">
        <div class="col-lg-12">&nbsp;</div>
        <h6><span>beta 1.1</span>(第<span class="countdown"></span>天)</h6>
    </div>
</header>

<div class="clearfix"></div>

<div id="ffz_app">
    <!--        items   -->
    <div class="col-lg-4" id="j_items">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-list"></span>事项列表(回头替换为目录名称)
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
                    <li class="list-group-item" v-on:click="getNotes(item)" draggable='true' @dragstart="drag(item)"
                        @dragover.prevent @drop="drop(item)">
                        <div class="checkbox">
                            <!--[v-model="something" is just syntactic sugar](http://stackoverflow.com/questions/41001192/setting-a-checkbox-as-checked-with-vue-js)-->
                            <!--                            v-model can only bind property -->
                            <input type="checkbox" v-model="item.status" v-on:click.stop="draft(item,index)"/>
                            <label for="checkbox">
                                <span><a :href="'/index/index?fid=' + item.id" target="_blank">{{item.id}}</a> </span>
                                <span v-show="!item.seen">{{item.name}}</span>
                                <input v-model="item.name" v-on:click.stop="" v-show="item.seen"
                                       v-on:keyup.esc="save(item)">
                            </label>
                        </div>
                        <div class="pull-right action-buttons">
                            <!--                                添加-->
                            <a v-on:click.stop="add()"><span class="glyphicon glyphicon-plus-sign" title="添加新事项"></span></a>
                            <!--                                编辑-->
                            <a v-on:click.stop="item.seen = !item.seen"><span
                                    class="glyphicon glyphicon-pencil" title="编辑事项"></span></a>
                            <a v-on:click.stop="save(item)"><span title="保存事项"
                                                                  class="glyphicon glyphicon-saved"></span></a>

                            <!--                                <a v-on:click.stop=""><span class="glyphicon glyphicon-flag"></span></a>-->
                            <a v-on:click.stop=""><span class="glyphicon glyphicon-warning-sign"
                                                        title="请慎重双击右边的删除按钮"></span></a>
                            <a v-on:click.stop="" v-on:dblclick.stop="del(index)"><span
                                    class="glyphicon glyphicon-trash" title="删除事项"></span></a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--        notes-->
    <div id="c_notes" class="panel-heading">
        <ul id="j_notes" class="col-lg-8">
            <!--            use :id to indict this is an auto_generated_id-->
            <li v-for="(note,index) in notes" :id="'note_' + note.id">
                <span>{{note.id}}</span>
                <span class="hidden-xs">{{note.c_time}}</span>
                <!--[You can surround the element with a template and use the v-if/v-else there:](https://jsfiddle.net/frfekkf5/7/)-->
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
                    <span v-on:click.stop="note.seen = !note.seen">
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
                    <textarea class="col-xs-12" v-if="note.seen" v-model="note.content" v-on:keyup.esc="save(note)"
                              v-on:keyup.enter="h($event,note)" @focus="h($event,note)" @paste="h($event,note)"
                              v-focus></textarea>
                    <div class="textarea" v-show="!note.seen" @dblclick.stop="note.seen = !note.seen"
                         v-html="compiledMarkdown(note)" v-highlightjs></div>
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
        // used
        loaddata: '/item/get-items',
        // used
        savefriend: '/item/save-item',
        // used
        deletefriend: "/item/delete-item",
        // used
        rank: '/item/rank',
        //itemDraft
        itemDraft: "/item/itemDraft",
        getnotes: '/item/get-item-notes',
        // move note
        movenote: '/note/movenote',
        savenote: "/note/save-note",
        deletenote: "/note/delete-note",

        // @todo 后面这几个还没有用到,留着备用
        changefriend: '/note/change-item',
        updatelatesttime: '/ajax/uplatesttime',
        webpage: 'index',
        linkto: "/index/recycle"
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