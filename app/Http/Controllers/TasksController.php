<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    public function index()
    {
        $data = [];   
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        
        return view('tasks.index', ['tasks' => $tasks, ]);    
        
      
        }
        
        // Welcomeビューでそれらを表示
        return view('welcome', $data);
        
       
    }
    
    
    public function create()
    {
        $task = new Task;

        
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    
     public function store(Request $request)
    {
       $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
            
        ]);
        
        // 認証済みユーザ（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
         $request->user()->tasks()->create([
             
            'status' => $request->status,
            'content' => $request->content,
            
           
        ]);
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $task = Task::findOrFail($id);
        
        return view('tasks.show', [
            'task' => $task,
        ]);
    }

   
   
    public function edit($id)
    {
        $task = Task::findOrFail($id);
         
        return view('tasks.edit', [
            'task' => $task,
        ]);    
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = \App\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }
        return redirect('/');
    }
}
    
