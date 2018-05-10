<?php
namespace n\modules\index\controllers;

use n\models\Notes;
use n\models\Tag;
use nxn\web\Ajax;
use n\modules\account\controllers\AuthController;

use PDO;

/**
 * Class AjaxController
 * @package n\modules\index
 * User: xiaoning nan
 * date:2017-05-24
 * time:08:24:16
 * Description: description
 */
class NoteController extends AuthController
{

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     * item_id | notes.item_id or item.id
     * limit | 每页显示几条
     * gage | 页数
     */
    public function getItemNotes()
    {
        $result = Notes::notesByItem($_REQUEST['item_id'], $_REQUEST);
        if ($_REQUEST) {
            Ajax::json(true, $result, 'success');
        } else {
            Ajax::json(0);
        }
    }

    /**
     * @access
     * @return string
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 暂且修改为彻底删除
     */
    public function deleteNote()
    {
        $note = Notes::load($_REQUEST['id']);
        if (null === $note) {
            Ajax::json(false, [], 'can not find related note wiht id:' . $_REQUEST['id']);
        }
        $deleted = $note->delete();
        return Ajax::json($deleted);
    }


    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     * id: notes.id, 如果没有值, 则执行插入操作
     * item_id: notes.item_id, namely items.id
     * content: notes.content
     */
    public function postNote()
    {
        $message = "";
        $note = new Notes();
        $note->setAttributes($_REQUEST);
        $status = $note->save(false);
        $id = $note->id;
        Ajax::json($status, ['id' => $id], $message);
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     */
    public function putMoveNote()
    {
        $note = Notes::load($_REQUEST['id']);
        if ($note === null) {
            Ajax::json(false);
            return;
        }
        $note->scenario = 'update';
        $note->setAttributes($_REQUEST);
        l(['type_of_item_id' => $note->item_id]);;
        $status = $note->update(false);
        l(['status' => $status]);
        Ajax::json($status);
    }

    public function putNoteDown(){
        $noteId = $_REQUEST['note_id'];
        $tagId = 1;
        $userId = $_SESSION['user_id'];
        $status = tag::addNoteTag($noteId, $tagId, $userId);
        Ajax::json($status);
    }
}