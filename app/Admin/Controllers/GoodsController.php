<?php

namespace App\Admin\Controllers;

use App\Model\GoodsModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GoodsController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->detail($id));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));

    }

    public function create(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form());
    }

    protected function grid()
    {
        $grid=new Grid(new GoodsModel());
        $grid->paginate('5');
        $grid->model()->orderBy('goods_id','desc');   //倒序排序
        $grid->goods_id('商品ID');
        $grid->goods_name('商品名称');
        $grid->store('商品库存');
        $grid->price('商品价格');
        $grid->updated_at('Updated at');
        $grid->add_time('添加时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });
        return $grid;
    }

    protected function detail($id)
    {

        $show = new Show(GoodsModel::findOrFail($id));

        $show->paginate(5);
        $show->goods_id('商品ID');
        $show->goods_name('商品名称');
        $show->store('商品库存');
        $show->price('商品价格');

        return $show;
    }

    protected function form(){
        $form=new Form(new GoodsModel());
        $form->display('goods_id','商品ID');
        $form->text('goods_name','商品名称');
        $form->number('store','商品库存');
        $form->currency('price','商品价格')->symbol('￥');
        $form->ckeditor('content');
        return $form;
    }

    //添加
    public function store()
    {
        $data=[
            'goods_name'=>$_POST['goods_name'],
            'store'=>$_POST['store'],
            'price'=>$_POST['price'],
            'add_time'=>time(),
        ];
        GoodsModel::insert($data);
    }

    //修改
    public function update($id)
    {
        $data=[
            'goods_name'=>$_POST['goods_name'],
            'store'=>$_POST['store'],
            'price'=>$_POST['price'],
            'add_time'=>time()
        ];
        GoodsModel::where(['goods_id'=>$id])->update($data);
    }

    //删除
    public function destroy($id)
    {
        GoodsModel::where(['goods_id'=>$id])->delete();
        $response = [
            'status' => true,
            'message'   => 'ok'
        ];
        return $response;
    }
}
