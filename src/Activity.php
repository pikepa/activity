<?php

namespace Pikepa\Activity;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Pikepa\Activity\ActivityLog;

trait Activity{
  
  public static function log($action,$model){
    
    //who
    $userNameColumn = config('activity.user_name_column');
    $user = isset(Auth::user()->$userNameColumn) ? Auth::user()->$userNameColumn :'guest';
    $ipAddress = Request::getClientIp();
    //what
    $modelName = class_basename($model);
    $modelId = $model->getKey();
    
    //how
    $payload = json_encode($model->getDirty());
    
    ActivityLog::create([
      'user' => $user,
      'ip_address'  => $ipAddress,
      'model_name'  =>$modelName,
      'model_id'    => $modelId,
      'payload'     => $payload,
      'action'      => $action
    ]);
    
    
    
    
  }
  
  public static function bootActivity(){
    
    static::created(function($model) {
        static::log('Created',$model);
    });    
    
    static::updated(function($model) {
        static::log('Updated',$model);
    });    
    
    static::deleted(function($model) {
        static::log('Deleted',$model);
    });
  }
}

