<?php
namespace model;

class Obr{
    public static function reg($data, $db){
        $check = mysqli_query($db, "SELECT `register_id` FROM `register` WHERE `register_email` = '{$data['register_email']}'");
        if(mysqli_num_rows($check) > 0){
            echo "<script>alert('Пользователь с таким email уже существует'); window.location='/reg';</script>";
            return;
        }
        
        $sql = mysqli_query($db, "INSERT INTO `register`(`register_email`, `register_pass`, `register_role`) VALUES ('{$data['register_email']}','{$data['register_pass']}','{$data['register_role']}')");
        $registerId = mysqli_insert_id($db);
        
        if(!$sql){
            echo "<script>alert('Ошибка регистрации'); window.location='/reg';</script>";
            return;
        }
        
        if($data['register_role'] === '1'){
            $userReg = mysqli_query($db, "INSERT INTO `user`(`register_id`, `user_lastname`, `user_name`, `user_midname`, `user_bdate`, `user_city`) 
            VALUES ('$registerId','{$data['user_lastname']}','{$data['user_name']}','{$data['user_midname']}','{$data['user_bdate']}','{$data['user_city']}')");
            $registerUserId = mysqli_insert_id($db);

            if(!$userReg){
                mysqli_query($db, "DELETE FROM `register` WHERE `register_id` = '$registerId'");
                echo "<script>alert('Ошибка регистрации пользователя'); window.location='/reg';</script>";
                return;
            }
            else{
                mysqli_query($db, "INSERT INTO `rating`(`user_id`, `event_category_id`, `scores`) VALUES ('$registerUserId','1','0')");
                mysqli_query($db, "INSERT INTO `rating`(`user_id`, `event_category_id`, `scores`) VALUES ('$registerUserId','2','0')");
                mysqli_query($db, "INSERT INTO `rating`(`user_id`, `event_category_id`, `scores`) VALUES ('$registerUserId','3','0')");
                mysqli_query($db, "INSERT INTO `rating`(`user_id`, `event_category_id`, `scores`) VALUES ('$registerUserId','4','0')");    
            }
        }
        elseif($data['register_role'] === '2'){
            $promoterReg = mysqli_query($db, "INSERT INTO `promoter`(`register_id`, `promoter_name`, `promoter_city`, `promoter_type`, `promoter_prize`, `promoter_eventcount`) 
            VALUES ('$registerId','{$data['promoter_name']}','{$data['promoter_city']}','{$data['promoter_type']}','-','0')");
            
            if(!$promoterReg){
                mysqli_query($db, "DELETE FROM `register` WHERE `register_id` = '$registerId'");
                echo "<script>alert('Ошибка регистрации организатора'); window.location='/reg';</script>";
                return;
            }
        }
        elseif($data['register_role'] === '3'){
            $coordinatorReg = mysqli_query($db, "INSERT INTO `talent_coordinator`(`register_id`, `talent_coordinator_lastname`, `talent_coordinator_name`, `talent_coordinator_midname`, `talent_coordinator_org`, `talent_coordinator_type`) 
            VALUES ('$registerId','{$data['talent_coordinator_lastname']}','{$data['talent_coordinator_name']}','{$data['talent_coordinator_midname']}','{$data['talent_coordinator_org']}','{$data['talent_coordinator_type']}')");
            
            if(!$coordinatorReg){
                mysqli_query($db, "DELETE FROM `register` WHERE `register_id` = '$registerId'");
                echo "<script>alert('Ошибка регистрации кадрового инспектора'); window.location='/reg';</script>";
                return;
            }
        }
        
        session_start();
        $_SESSION['register_id'] = $registerId;
        $_SESSION['register_role'] = $data['register_role'];
        echo "<script>alert('Регистрация успешна!'); window.location='/profile';</script>";
    }

    public static function login($data, $db){
        $check = mysqli_query($db, "SELECT `register_id`, `register_pass`, `register_role` FROM `register` WHERE `register_email` = '{$data['login_email']}'");
        
        if(mysqli_num_rows($check) == 0){
            echo "<script>alert('Неверный логин или пароль'); window.location='/login';</script>";
            return;
        }
        
        $user = mysqli_fetch_assoc($check);
        
        if($user['register_pass'] != $data['login_pass']){
            echo "<script>alert('Неверный логин или пароль'); window.location='/login';</script>";
            return;
        }
        
        session_start();
        $_SESSION['register_id'] = $user['register_id'];
        $_SESSION['register_role'] = $user['register_role'];
        
        echo "<script>alert('Вход выполнен'); window.location='/profile';</script>";
    }

    public static function addEvent($data, $db){
        session_start();
        $register_id = $_SESSION['register_id'];
        $promoterQuery = mysqli_query($db, "SELECT `promoter_id` FROM `promoter` WHERE `register_id` = '$register_id'");
        $promoter = mysqli_fetch_assoc($promoterQuery);
        $promoter_id = $promoter['promoter_id'];
        
        $sql = mysqli_query($db, "INSERT INTO `event`(`promoter_id`, `event_category_id`, `difficulty_id`, `status_id`, `event_name`, `event_descr`, `event_date`, `event_format`, `base_score`, `firstp_score`, `secondp_score`, `thirdp_score`, `event_bonus`) 
        VALUES ('{$promoter_id}','{$data['event_category_id']}','{$data['difficulty_id']}','1','{$data['event_name']}','{$data['event_descr']}','{$data['event_date']}','{$data['event_format']}','{$data['base_score']}','{$data['firstp_score']}','{$data['secondp_score']}','{$data['thirdp_score']}','{$data['event_bonus']}')");
        
        if(!$sql){
            echo "<script>alert('Ошибка создания мероприятия'); window.location='/event_new';</script>";
            return;
        }
        
        echo "<script>alert('Мероприятие отправлено на модерацию!'); window.location='/profile';</script>";
    }

    public static function joinEvent($data, $db){
        session_start();
        $user_id = $data['user_id'];
        $event_id = $data['event_id'];
        
        $eventQuery = mysqli_query($db, "SELECT `status_id` FROM `event` WHERE `event_id` = '$event_id'");
        $event = mysqli_fetch_assoc($eventQuery);
        
        if($event['status_id'] != 2){
            echo "<script>alert('Набор на мероприятие закрыт'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        $check = mysqli_query($db, "SELECT * FROM `participation` WHERE `user_id` = '$user_id' AND `event_id` = '$event_id'");
        if(mysqli_num_rows($check) > 0){
            echo "<script>alert('Вы уже зарегистрированы на это мероприятие'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        $sql = mysqli_query($db, "INSERT INTO `participation`(`user_id`, `event_id`, `attendance`, `place`) 
        VALUES ('$user_id','$event_id','0','-')");
        
        if(!$sql){
            echo "<script>alert('Ошибка регистрации на мероприятие'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        echo "<script>alert('Вы успешно зарегистрированы на мероприятие!'); window.location='/event?id=$event_id';</script>";
    }
    
    public static function finishEvent($data, $db){
        session_start();
        $event_id = $data['event_id'];
        
        $sql = mysqli_query($db, "UPDATE `event` SET `status_id` = '4' WHERE `event_id` = '$event_id'");
        
        if(!$sql){
            echo "<script>alert('Ошибка завершения мероприятия'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        echo "<script>window.location='/rateEvent?id=$event_id';</script>";
    }   

    public static function saveResults($data, $db){
        session_start();
        $event_id = $data['event_id'];
        
        $eventQuery = mysqli_query($db, "SELECT * FROM `event` WHERE `event_id` = '$event_id'");
        $event = mysqli_fetch_assoc($eventQuery);
        
        $difficultyQuery = mysqli_query($db, "SELECT * FROM `difficulty` WHERE `difficulty_id` = '{$event['difficulty_id']}'");
        $difficulty = mysqli_fetch_assoc($difficultyQuery);
        
        foreach($data['results'] as $user_id => $result){
            $attendance = $result['attendance'];
            $place = isset($result['place']) && $result['place'] != '' ? $result['place'] : '';
            
            mysqli_query($db, "UPDATE `participation` SET `attendance` = '$attendance', `place` = '$place' WHERE `user_id` = '$user_id' AND `event_id` = '$event_id'");
            
            if($attendance == 1){
                $score = $event['base_score'] * $difficulty['difficulty_coefficient'];
                
                if($place == 1){
                    $score += $event['firstp_score'] * $difficulty['difficulty_coefficient'];
                } elseif($place == 2){
                    $score += $event['secondp_score'] * $difficulty['difficulty_coefficient'];
                } elseif($place == 3){
                    $score += $event['thirdp_score'] * $difficulty['difficulty_coefficient'];
                }
                
                $checkGeneralRating = mysqli_query($db, "SELECT * FROM `rating` WHERE `user_id` = '$user_id' AND `event_category_id` = '4'");
                if(mysqli_num_rows($checkGeneralRating) > 0){
                    mysqli_query($db, "UPDATE `rating` SET `scores` = `scores` + '$score' WHERE `user_id` = '$user_id' AND `event_category_id` = '4'");
                } else {
                    mysqli_query($db, "INSERT INTO `rating`(`user_id`, `event_category_id`, `scores`) VALUES ('$user_id','4','$score')");
                }
                
                $checkCategoryRating = mysqli_query($db, "SELECT * FROM `rating` WHERE `user_id` = '$user_id' AND `event_category_id` = '{$event['event_category_id']}'");
                if(mysqli_num_rows($checkCategoryRating) > 0){
                    mysqli_query($db, "UPDATE `rating` SET `scores` = `scores` + '$score' WHERE `user_id` = '$user_id' AND `event_category_id` = '{$event['event_category_id']}'");
                } else {
                    mysqli_query($db, "INSERT INTO `rating`(`user_id`, `event_category_id`, `scores`) VALUES ('$user_id','{$event['event_category_id']}','$score')");
                }
                
                $change_date = date('Y-m-d H:i:s');
                mysqli_query($db, "INSERT INTO `rating_change`(`user_id`, `event_category_id`, `scores_change`, `change_date`) 
                VALUES ('$user_id','{$event['event_category_id']}','$score','$change_date')");
                mysqli_query($db, "INSERT INTO `rating_change`(`user_id`, `event_category_id`, `scores_change`, `change_date`) 
                VALUES ('$user_id','4','$score','$change_date')");
            }
        }
        
        echo "<script>alert('Результаты сохранены!'); window.location='/profile';</script>";
    }
    
    public static function updateProfile($data, $db){
        session_start();
        $register_id = $_SESSION['register_id'];
        $role = $_SESSION['register_role'];
        
        $email = $data['register_email'];
        mysqli_query($db, "UPDATE `register` SET `register_email` = '$email' WHERE `register_id` = '$register_id'");
        
        if(isset($data['new_password']) && $data['new_password'] != ''){
            $password = $data['new_password'];
            mysqli_query($db, "UPDATE `register` SET `register_pass` = '$password' WHERE `register_id` = '$register_id'");
        }
        
        if($role == '1'){
            $lastname = $data['user_lastname'];
            $name = $data['user_name'];
            $midname = $data['user_midname'];
            $bdate = $data['user_bdate'];
            $city = $data['user_city'];
            
            mysqli_query($db, "UPDATE `user` SET 
                `user_lastname` = '$lastname',
                `user_name` = '$name',
                `user_midname` = '$midname',
                `user_bdate` = '$bdate',
                `user_city` = '$city'
                WHERE `register_id` = '$register_id'");
        } elseif($role == '2'){
            $promoter_name = $data['promoter_name'];
            $promoter_city = $data['promoter_city'];
            $promoter_type = $data['promoter_type'];
            
            mysqli_query($db, "UPDATE `promoter` SET 
                `promoter_name` = '$promoter_name',
                `promoter_city` = '$promoter_city',
                `promoter_type` = '$promoter_type'
                WHERE `register_id` = '$register_id'");
        } elseif($role == '3'){
            $lastname = $data['talent_coordinator_lastname'];
            $name = $data['talent_coordinator_name'];
            $midname = $data['talent_coordinator_midname'];
            $org = $data['talent_coordinator_org'];
            $type = $data['talent_coordinator_type'];
            
            mysqli_query($db, "UPDATE `talent_coordinator` SET 
                `talent_coordinator_lastname` = '$lastname',
                `talent_coordinator_name` = '$name',
                `talent_coordinator_midname` = '$midname',
                `talent_coordinator_org` = '$org',
                `talent_coordinator_type` = '$type'
                WHERE `register_id` = '$register_id'");
        }
        
        echo "<script>alert('Профиль обновлен!'); window.location='/profile';</script>";
    }
    
    public static function logout($data, $db){
        session_start();
        session_destroy();
        echo "<script>alert('Вы вышли из системы'); window.location='/';</script>";
    }

    public static function addReview($data, $db){
        session_start();
        $user_id = $data['user_id'];
        $event_id = $data['event_id'];
        $review_text = $data['review_text'];
        $review_score = $data['review_score'];
        
        $checkParticipation = mysqli_query($db, "SELECT * FROM `participation` WHERE `user_id` = '$user_id' AND `event_id` = '$event_id' AND `attendance` = '1'");
        if(mysqli_num_rows($checkParticipation) == 0){
            echo "<script>alert('Вы можете оставить отзыв только на мероприятия, в которых участвовали'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        $checkReview = mysqli_query($db, "SELECT * FROM `review` WHERE `user_id` = '$user_id' AND `event_id` = '$event_id'");
        if(mysqli_num_rows($checkReview) > 0){
            echo "<script>alert('Вы уже оставили отзыв на это мероприятие'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        $sql = mysqli_query($db, "INSERT INTO `review`(`user_id`, `event_id`, `review_text`, `review_score`) 
        VALUES ('$user_id','$event_id','$review_text','$review_score')");
        
        if(!$sql){
            echo "<script>alert('Ошибка добавления отзыва'); window.location='/event?id=$event_id';</script>";
            return;
        }
        
        $eventQuery = mysqli_query($db, "SELECT `promoter_id` FROM `event` WHERE `event_id` = '$event_id'");
        $event = mysqli_fetch_assoc($eventQuery);
        $promoter_id = $event['promoter_id'];
        
        $reviewsQuery = mysqli_query($db, "SELECT AVG(`review_score`) as avg_score FROM `review` r 
            JOIN `event` e ON r.event_id = e.event_id 
            WHERE e.promoter_id = '$promoter_id'");
        $avgScore = mysqli_fetch_assoc($reviewsQuery);
        $newRating = round($avgScore['avg_score'], 1);
        
        mysqli_query($db, "UPDATE `promoter` SET `promoter_rating` = '$newRating' WHERE `promoter_id` = '$promoter_id'");
        
        echo "<script>alert('Отзыв добавлен! Спасибо за оценку'); window.location='/event?id=$event_id';</script>";
    }

    public static function downloadReport($data, $db){
        session_start();
        $user_id = $data['user_id'];
        
        $userQuery = mysqli_query($db, "SELECT u.*, r.register_email 
            FROM `user` u 
            JOIN `register` r ON u.register_id = r.register_id 
            WHERE u.user_id = '$user_id'");
        $user = mysqli_fetch_assoc($userQuery);
        
        $generalRating = mysqli_fetch_assoc(mysqli_query($db, "SELECT `scores` FROM `rating` WHERE `user_id` = '$user_id' AND `event_category_id` = '4'"));
        $generalScore = $generalRating ? $generalRating['scores'] : 0;
        
        $placeQuery = mysqli_query($db, "SELECT COUNT(*) + 1 as place FROM `rating` WHERE `event_category_id` = '4' AND `scores` > '$generalScore'");
        $place = mysqli_fetch_assoc($placeQuery);
        
        $ratings = mysqli_query($db, "SELECT r.*, ec.event_category_name 
            FROM `rating` r 
            JOIN `event_category` ec ON r.event_category_id = ec.event_category_id 
            WHERE r.user_id = '$user_id' AND r.event_category_id != '4'");
        
        $participations = mysqli_query($db, "SELECT p.*, e.event_name, e.event_date, ec.event_category_name 
            FROM `participation` p 
            JOIN `event` e ON p.event_id = e.event_id 
            JOIN `event_category` ec ON e.event_category_id = ec.event_category_id 
            WHERE p.user_id = '$user_id' 
            ORDER BY e.event_date DESC");
        
        $totalEvents = mysqli_num_rows($participations);
        $attendedEvents = mysqli_num_rows(mysqli_query($db, "SELECT * FROM `participation` WHERE `user_id` = '$user_id' AND `attendance` = '1'"));
        $wins = mysqli_num_rows(mysqli_query($db, "SELECT * FROM `participation` WHERE `user_id` = '$user_id' AND `place` IN ('1','2','3')"));
        
        $html = "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Отчет о пользователе</title>
            <style>
                body { font-family: Arial, sans-serif; }
                h1 { color: #333; }
                h2 { color: #555; }
                table { border-collapse: collapse; width: 100%; margin: 10px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .info { background-color: #f9f9f9; padding: 10px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <h1>Отчет о пользователе</h1>
            <p><strong>Дата формирования:</strong> " . date('d.m.Y H:i:s') . "</p>
            
            <h2>Личные данные</h2>
            <div class='info'>
                <p><strong>ФИО:</strong> {$user['user_lastname']} {$user['user_name']} {$user['user_midname']}</p>
                <p><strong>Email:</strong> {$user['register_email']}</p>
                <p><strong>Дата рождения:</strong> {$user['user_bdate']}</p>
                <p><strong>Город:</strong> {$user['user_city']}</p>
            </div>
            
            <h2>Рейтинг</h2>
            <div class='info'>
                <p><strong>Общий рейтинг:</strong> {$generalScore} баллов</p>
                <p><strong>Место в топе:</strong> {$place['place']} место</p>
            </div>
            
            <h3>Рейтинг по направлениям</h3>
            <table>
                <thead>
                    <tr><th>Направление</th><th>Баллы</th></tr>
                </thead>
                <tbody>";
        
        while($rating = mysqli_fetch_assoc($ratings)){
            $html .= "<tr><td>{$rating['event_category_name']}</td><td>{$rating['scores']}</td></tr>";
        }
        
        $html .= "</tbody>
            </table>
            
            <h2>Статистика участия</h2>
            <div class='info'>
                <p><strong>Всего мероприятий:</strong> {$totalEvents}</p>
                <p><strong>Участий:</strong> {$attendedEvents}</p>
                <p><strong>Призовых мест:</strong> {$wins}</p>
            </div>
            
            <h2>Участие в мероприятиях</h2>
            <table>
                <thead>
                    <tr><th>Название</th><th>Дата</th><th>Категория</th><th>Участие</th><th>Место</th></tr>
                </thead>
                <tbody>";
        
        mysqli_data_seek($participations, 0);
        while($part = mysqli_fetch_assoc($participations)){
            $attendance_text = $part['attendance'] == 1 ? 'Участвовал' : 'Не участвовал';
            $place_text = ($part['place'] != '-' && $part['place'] != '') ? $part['place'] . ' место' : '-';
            $html .= "<tr>
                <td>{$part['event_name']}</td>
                <td>{$part['event_date']}</td>
                <td>{$part['event_category_name']}</td>
                <td>{$attendance_text}</td>
                <td>{$place_text}</td>
            </tr>";
        }
        
        $html .= "</tbody>
            </table>
        </body>
        </html>";
        
        $filename = "report_user_{$user_id}_" . date('Y-m-d') . ".html";
        file_put_contents($filename, $html);
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($filename);
        unlink($filename);
        exit();
    }
}