<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\Comments\Model\Comment;
use SilverStripe\Core\Extension;

/**
 * Adds Blog specific behaviour to Comment.
 *
 * @extends Extension<Comment>
 */
class BlogCommentExtension extends Extension
{
    /**
     * Extra CSS classes for styling different comment types.
     *
     * @return string
     */
    public function getExtraClass()
    {
        $blogPost = $this->owner->Parent();

        if ($blogPost instanceof BlogPost) {
            if ($blogPost->isAuthor($this->owner->Author())) {
                return 'author-comment';
            }
        }

        return '';
    }
}
