<?php

namespace app\models;

use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $date
 * @property string $image
 * @property int $viewed
 * @property int $user
 * @property int $status
 * @property int $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'content'], 'string'],
            [['date'], 'safe'],
            [['viewed', 'user', 'status', 'category_id'], 'integer'],
            [['title', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'date' => 'Date',
            'image' => 'Image',
            'viewed' => 'Viewed',
            'user' => 'User',
            'status' => 'Status',
            'category_id' => 'Category ID',
        ];
    }

    public function saveImage($fileName) {
        $this->image = $fileName;
        return $this->save(false);
    }

    public function getImage() {
        
        return $this->image ? '/web/uploads/' . $this->image : '/web/no-image.png';
    }

    public function deleteImage(){
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }
    public function beforeDelete()
    {
        $this-> deleteImage();
        return parent::beforeDelete();
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function saveCategory($category_id) {
        $category = Category::findOne($category_id);
        if($category != null) {
            $this->link('category', $category);
            return true;
        }
        

    }

    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('article_tag', ['article_id' => 'id']);
    }
    public function getSelectedTags() {
        return $selectedIds = $this->getTags()->select('id')->asArray()->all();
        ArrayHelper::getColumn( $selectedIds, 'id');
        
    }
    public function saveTags($tags) {
        if(is_array($tags)) {
            $this->clearCurrentTags();
            foreach($tags as $tag_id) {
                $tag = Tag::findOne($tag_id);
                $this->link('tags', $tag);
            }
        }
    }
    public function clearCurrentTags() {
        ArticleTag::deleteAll(['article_id' => $this->id]);
    }

    public function getDate() {
        // Yii::$app->formatter->locale = 'ru-RU';
        return Yii::$app->formatter->asDate($this->date);
    }

    public static function getAll($pageSize = 5) {
        // build a DB query to get all articles with status = 1
        $query = Article::find();

        // get the total number of articles (but do not fetch the article data yet)
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);

        // limit the query using the pagination and retrieve the articles
        $articles = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
            $data['articles'] = $articles;
            $data['pagination'] = $pagination;
            return $data;
    }
    public static function getPopular() {
        return Article::find()->orderBy('viewed desc')->limit(3)->all();
    }
    public static function getRecent() {
        return Article::find()->orderBy('date asc')->limit(4)->all();
    }

    public function saveArticle() {
        $this->user = Yii::$app->user->id;
        return $this->save();
    }

    public function getComments() {
        return $this->hasMany(Comment::className(), ['article_id' => 'id']);
    }
    public function getArticleComments() {
        return $this->getComments()->where(['status' => 1])->all();
    }
    public function getauthor() {
        return $this->hasOne(User::className(), ['id'=> 'user']);
    }
    public function viewedCounter() {
        $this->viewed += 1;
        return $this->save(false);
    }
    
 
}
