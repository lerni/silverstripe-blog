<?php

namespace SilverStripe\Blog\Model;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataObject;
use SilverStripe\TagField\TagField;

/**
 * A blog tag for keyword descriptions of a blog post.
 *
 * @property string $Title
 * @property string $URLSegment
 * @property int $BlogID
 * @method Blog Blog()
 */
class BlogTag extends DataObject implements CategorisationObject
{
    use BlogObject;

    private static bool $allow_urlsegment_multibyte = true;

    /**
     * Use an exception code so that attempted writes can continue on
     * duplicate errors.
     *
     * @const string
     * This must be a string because ValidationException has decided we can't use int
     */
    const DUPLICATE_EXCEPTION = 'DUPLICATE';

    /**
     * {@inheritDoc}
     * @var string
     */
    private static $table_name = 'BlogTag';

    /**
     * @var array
     */
    private static $db = [
        'Title'      => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Blog' => Blog::class
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'BlogPosts' => BlogPost::class
    ];

    public function scaffoldFormFieldForHasMany(
        string $relationName,
        ?string $fieldTitle,
        DataObject $ownerRecord,
        bool &$includeInOwnTab
    ): FormField {
        $includeInOwnTab = false;
        return $this->scaffoldFormFieldForManyRelation($relationName, $fieldTitle, $ownerRecord);
    }

    public function scaffoldFormFieldForManyMany(
        string $relationName,
        ?string $fieldTitle,
        DataObject $ownerRecord,
        bool &$includeInOwnTab
    ): FormField {
        $includeInOwnTab = false;
        return $this->scaffoldFormFieldForManyRelation($relationName, $fieldTitle, $ownerRecord);
    }

    private function scaffoldFormFieldForManyRelation(
        string $relationName,
        ?string $fieldTitle,
        DataObject $ownerRecord
    ): FormField {
        $parent = ($ownerRecord instanceof SiteTree) ? $ownerRecord->Parent() : null;
        $field = TagField::create(
            $relationName,
            /** @phpstan-ignore translation.key (we need the key to be dynamic here) */
            _t($ownerRecord->ClassName . '.' . $relationName, $fieldTitle),
            ($parent instanceof Blog) ? $parent->Tags() : static::get(),
            $ownerRecord->$relationName()
        )->setShouldLazyLoad(true);
        if ($ownerRecord instanceof BlogPost) {
            $field->setCanCreate($ownerRecord->canCreateTags());
        }
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListUrlSegment()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDuplicateError()
    {
        return _t(__CLASS__ . '.Duplicate', 'A blog tag already exists with that name.');
    }
}
