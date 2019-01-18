<?php

namespace App\Admin\Controllers;

use App\Model\UserModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserModel);

        $grid->uid('Uid');
        $grid->name('Name');
        $grid->age('Age');
        $grid->email('Email');
        $grid->reg_time('Reg time')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UserModel::findOrFail($id));

        $show->uid('Uid');
        $show->name('Name');
        $show->age('Age');
        $show->email('Email');
        $show->reg_time('Reg time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserModel);

        $form->number('uid', 'Uid');
        $form->text('name', 'Name');
        $form->number('age', 'Age');
        $form->email('email', 'Email');
        $form->number('reg_time', 'Reg time');

        return $form;
    }

    //添加
    public function store()
    {
        $data=[
            'name'=>$_POST['name'],
            'age'=>$_POST['age'],
            'email'=>$_POST['email'],
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
