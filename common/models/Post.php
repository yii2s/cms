<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $content
 */
class Post extends \yii\db\ActiveRecord {

    const STATUS_PRIVATE = 'private';
    const STATUS_PUBLISH = 'publish';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'posts';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['description', 'content', 'image'], 'string'],
            [['title', 'type', 'slug', 'status'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'category_id' => 'Danh mục',
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'content' => 'Nội dung',
            'image' => 'Hình ảnh',
            'created_at' => 'Ngày tạo',
            'status' => 'Trạng thái'
        ];
    }

    public function behaviors() {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'slug' => [
                'class' => 'yii\behaviors\SluggableBehavior',
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'immutable' => true,
                'ensureUnique' => true
            ],
        ]);
    }

    public function getPostStatus() {
        return [
            self::STATUS_PUBLISH => 'Mọi người',
            self::STATUS_PRIVATE => 'Riêng tư',
        ];
    }

    public function getCategories(&$data = [], $parent = NULL) {
        $category = Category::find()->where(['parent_id' => $parent, 'type' => 'post'])->all();
        foreach ($category as $key => $value) {
            $data[$value->id] = $this->getIndent($value->indent) . $value->title;
            unset($category[$key]);
            $this->getCategories($type, $data, $value->id);
        }
        return $data;
    }

    public function getIndent($int) {
        if ($int > 0) {
            for ($index = 1; $index <= $int; $index++) {
                $data[] = '—';
            }
            return implode('', $data) . ' ';
        } else
            return '';
    }

}
