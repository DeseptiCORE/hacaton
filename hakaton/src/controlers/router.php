<?php
use routers\Router;
use model\Obr;

Router::postmetnod('/reg',Obr::class,'reg',$_POST);
Router::postmetnod('/login',Obr::class,'login',$_POST);
Router::postmetnod('/addEvent',Obr::class,'addEvent',$_POST);
Router::postmetnod('/joinEvent',Obr::class,'joinEvent',$_POST);
Router::postmetnod('/finishEvent',Obr::class,'finishEvent',$_POST);
Router::postmetnod('/saveResults',Obr::class,'saveResults',$_POST);
Router::postmetnod('/moderateEvent',Obr::class,'moderateEvent',$_POST);
Router::postmetnod('/updateProfile',Obr::class,'updateProfile',$_POST);
Router::postmetnod('/addReview',Obr::class,'addReview',$_POST);
Router::postmetnod('/downloadReport',Obr::class,'downloadReport',$_POST);


Router::getmetnod('/profile','profile_page');
Router::getmetnod('/user_profile','user_profile_page');
Router::getmetnod('/','home_page');
Router::getmetnod('/reg','reg_page');
Router::getmetnod('/login','login_page');
Router::getmetnod('/event_new','event_new_page');
Router::getmetnod('/event','event_page');
Router::getmetnod('/events','events_page');
Router::getmetnod('/rateEvent','rate_event_page');
Router::getmetnod('/logout','logout_page');
Router::getmetnod('/top_users','top_users_page');
Router::getmetnod('/user_stats','user_stats_page');
Router::getmetnod('/organizers','organizers_page');
Router::action();