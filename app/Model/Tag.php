<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Input;
class Tag extends Model {

	//
    protected $table = 'tags';

    protected $fillable = [
        'name',
        'number'
    ];

    public $timestamps = false;

    /**
     * 获取所有标签
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getTagModelAll(){
        return self::all();
    }

    /**
     * 获取标签插件所需得列表数据
     * @return null|string
     */
    public static function getTagStringAll(){
        $tags = self::getTagModelAll();
        return !empty($tags)?self::TagModelConversionTagString($tags):null;
    }


    /**
     * 根据标签id串获取标签数据
     * @param string $tagIds
     * @return \Illuminate\Support\Collection|null|static
     */
    public static function getTagModelByTagIds($tagIds){
        $tags = explode(',',$tagIds);
        return !empty($tags)?self::find($tags):null;

    }

    /**
     * 根据标签id串获取标签插件所需得数据
     * @param $tagIds
     * @return null|string
     */
    public static function getTagStringByTagIds($tagIds){
        $tags = self::getTagModelByTagIds($tagIds);
        return !empty($tags)?self::TagModelConversionTagString($tags):null;
    }

    /**
     * 根据标签model，把标签转换成标签插件所需得数据
     * @param Object $result
     * @return string
     */
    public static function TagModelConversionTagString($result){
        $tag = '';
        if(!empty($result)){
            $tag = "[";
            foreach($result as $k=>$v){
                $tag .= "'$v->name',";
            }
            $tag = trim($tag,',');
            $tag .= ']';
        }
        return $tag;
    }

    /**
     * 自动插入标签
     * @param $tags
     * @param $new_tags
     * @return string
     */
    public static function SetArticleTags($tags,$new_tags){
        $tagsArr = array();
        if(!empty($tags)){
            $tagsArr = explode(',',$tags);
        }
        $new_tagsArr = array();
        if(!empty($new_tags)){
            $new_tagsArr = explode(',',$new_tags);
        }
        $tag = array_merge($tagsArr,$new_tagsArr);
        $tagIds = array();
        if(!empty($tag)){
            foreach($tag as $K=>$v){
                $tag_temp = self::where('name','=',trim($v))->first();
                if($tag_temp){
                    $tag_temp->number += 1;
                    $tag_temp->save();
                    $tagIds[] = $tag_temp->id;
                }else{
                    // insert
                    $tagIds[] = self::insertGetId(['name'=>$v,'number'=>1]);
                }
            }
            unset($tag_temp);
        }
        return implode(',',$tagIds);
    }

    public static function setFieldData(){
        $fieldData = array();
        $tag = new Tag();
        $arr = $tag->getFillable();
        foreach($arr as $v){
            $fieldData[$v] = Input::get($v);
        }
        unset($arr);
        unset($fieldData['number']);
        unset($tag);
        return $fieldData;
    }

    /**
     * 获取热门标签
     * @param $limit
     * @return mixed
     */
    public static function getHotTags($limit){
        return self::orderBy('number','DESC')->limit($limit)->get();
    }

}
