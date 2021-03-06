<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Http\Requests\UserRequest;

use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    //除了此处指定的动作以外，所有其他动作都必须登录用户才能访问
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    //展示个人页面
    public function show(User $user){
        return view('users.show', compact('user'));
    }
    //展示编辑压面
    public function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    //编辑页面提交的数据
    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user){
        // $user->update($request->all());
        $this->authorize('update', $user);
        $data = $request->all();
        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 416);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功');
    }
}
