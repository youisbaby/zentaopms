<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'section' . DS . 'v1.php';

class history extends wg
{
    protected static array $defineProps = array(
        'actions?: array',
        'users?: array',
        'methodName?: string',
        'commentUrl?: string',
        'commentBtn?: bool'
    );

    public static function getPageCSS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    private function marker(int $num): wg
    {
        return span
        (
            setClass('marker', 'relative', 'z-10', 'text-sm', 'rounded-full', 'aspect-square', 'inline-flex', 'justify-center', 'items-center', 'mr-1', 'border', 'h-5', 'w-5', 'z-10'),
            $num
        );
    }

    private function checkEditCommentPriv(object $action): bool
    {
        global $app;
        $methodName = $this->prop('methodName') !== null ? $this->prop('methodName') : $app->rawMethod;
        $actions    = $this->prop('actions') !== null ? $this->prop('actions') : data('actions');

        return (!isset($canBeChanged) || !empty($canBeChanged))
            && !empty($actions) && end($actions) == $action
            && trim($action->comment) !== ''
            && str_contains(',view,objectlibs,viewcard,', ",$methodName,")
            && $action->actor == $app->user->account
            && common::hasPriv('action', 'editComment');
    }

    private function expandBtn(): wg
    {
        global $lang;
        return btn
        (
            setClass('btn-expand btn-mini px-0'),
            set::title($lang->switchDisplay),
            h::i(setClass('change-show icon icon-plus icon-sm')),
            on::click('expand'),
        );
    }

    private function editCommentBtn(): wg
    {
        global $lang;
        return button
        (
            setClass('btn btn-link btn-edit-comment right-0'),
            set::title($lang->action->editComment),
            h::i(setClass('icon icon-pencil')),
            on::click('editComment')
        );
    }

    private function historyChanges(object $action): wg
    {
        global $app;
        return div
        (
            setClass('history-changes ml-7 mt-2'),
            html($app->loadTarget('action')->renderChanges($action->objectType, $action->history)),
        );
    }

    private function actionItem(object $action, int $i): wg
    {
        global $app;
        return li
        (
            setClass('mb-2'),
            set::value($i),
            $this->marker($i),
            html($app->loadTarget('action')->renderAction($action))
        );
    }

    private function getComment(object $action): string
    {
        if(str_contains($action->comment, '<pre class="prettyprint lang-html">'))
        {
            $before   = explode('<pre class="prettyprint lang-html">', $action->comment);
            $after    = explode('</pre>', $before[1]);
            $htmlCode = $after[0];
            return $before[0] . htmlspecialchars($htmlCode) . $after[1];
        }

        return strip_tags($action->comment) === $action->comment
            ? nl2br($action->comment)
            : $action->comment;
    }

    private function comment(object $action): wg
    {
        $comment = $this->getComment($action);
        $canEdit = $this->checkEditCommentPriv($action);
        return div
        (
            setClass('article-content comment relative'),
            $canEdit ? $this->editCommentBtn() : null,
            div
            (
                setClass('comment-content mt-2 ml-6 p-2.5'),
                isHTML($comment) ? html($comment) : $comment,
            ),
        );
    }

    private function commentEditForm(object $action): wg
    {
        global $lang;

        return form
        (
            setClass('comment-edit-form hidden mt-2 ml-6'),
            set::method('post'),
            set::action(createLink('action', 'editComment', "actionID=$action->id")),
            editor
            (
                set::name('lastComment'),
                isHTML($action->comment) ? html($action->comment) : $action->comment
            ),
            set::actions(array(
                'submit',
                array('text' => $lang->close, 'id' => 'btn-close-form')
            ))
        );
    }

    private function historyList(): wg
    {
        $actions = $this->prop('actions') !== null ? $this->prop('actions') : data('actions');
        $users   = $this->prop('users') !== null ? $this->prop('users') : data('users');
        $historiesListView = h::ol(setClass('history-list col relative'));

        $i = 0;
        foreach($actions as $action)
        {
            if($action->action === 'assigned' || $action->action === 'toaudit') $action->extra = zget($users, $action->extra);
            $action->actor = zget($users, $action->actor);
            if(str_contains($action->actor, ':')) $action->actor = substr($action->actor, strpos($action->actor, ':') + 1);

            $i++;
            $actionItemView = $this->actionItem($action, $i);

            if(!empty($action->history))
            {
                $actionItemView->add($this->expandBtn());
                $actionItemView->add($this->historyChanges($action));
            }
            if(strlen(trim(($action->comment))) !== 0)
            {
                $actionItemView->add($this->comment($action));

                $canEditComment = $this->checkEditCommentPriv($action);
                if($canEditComment) $actionItemView->add($this->commentEditForm($action));
            }
            $historiesListView->add($actionItemView);
        }

        return $historiesListView;
    }

    private function reverseBtn(): wg
    {
        global $lang;
        return btn
        (
            setClass('btn-reverse btn-mini px-0 ml-3'),
            set::title($lang->reverse),
            set::icon('arrow-up'),
            on::click('reverseList')
        );
    }

    private function expandAllBtn(): wg
    {
        global $lang;
        return btn
        (
            setClass('btn-mini px-0 btn-expand-all ml-2'),
            set::title($lang->switchDisplay),
            set::icon('plus'),
            on::click('expandAll')
        );
    }

    private function commentBtn(): ?wg
    {
        global $app, $lang;
        $methodName = $this->prop('methodName') !== null ? $this->prop('methodName') : $app->rawMethod;
        $showCommentBtn = $this->prop('commentBtn', false);
        if(!$showCommentBtn && !str_contains(',view,objectlibs,viewcard,', ",$methodName,")) return null;
        return commentBtn
        (
            set::dataTarget('#comment-dialog'),
            setClass('btn-comment ml-4 size-sm ghost'),
            set::icon('chat-line'),
            set::iconClass('text-primary'),
            set::text($lang->action->create)
        );
    }

    protected function build(): wg
    {
        global $lang;

        $commentUrl = $this->prop('commentUrl');
        $isInModal  = isAjaxRequest('modal');
        $px = $isInModal ? 'px-3' : 'px-6';
        $pb = $isInModal ? 'pb-3' : 'pb-6';

        return new section
        (
            setClass('history', 'pt-4', $px, $pb, 'canvas'),
            set::title($lang->history),
            to::actions
            (
                div
                (
                    setClass('flex items-center'),
                    $this->reverseBtn(),
                    $this->expandAllBtn(),
                    $this->commentBtn(),
                )
            ),
            div(setClass('mt-3'), $this->historyList()),
            commentDialog
            (
                set::name('comment'),
                set::url($commentUrl),
            )
        );
    }
}
