<?php
/**
* DatabaseConnection
*
* @uses  SINGLETON PDO wrapper class   
*
* @author   Cagil Ozdemirag
* 
*   Improved by Thamer Alshammari on 28/10/13
*   Improved by Cagil Ozdemirag and Thamer Alshammari on 1/11/13
*/
class DatabaseConnection{


    /**
     * $PDO
     *
     * @var PHP Database Object
     *
     * @access private
     * @static
     */
	private $PDO = null;


    /**
     * $singleton
     *
     * @var DatabaseConnection singleton object
     *
     * @access private
     * @static
     */
	private static $singleton = null;

    /**
     * getInstance
     *
     * @uses initiator for this Singleton class. 
     * @uses Call this method as DatabaseConnection::getInstance(), then call any other method.
     * 
     * @access public
     * @static
     *
     * @return mixed Value.
     */
	public static function getInstance(){

		if(self::$singleton === null){
			self::$singleton = new DatabaseConnection();
		}

		return self::$singleton;
	}

   

    /**
     * __construct
     * 
     * @uses private constructor for Singleton purposes, to prevent creating instances of this class outside this class with 'new' keyword.
     *
     * @access private
     *
     * @return <none>
     */
    private function __construct(){
    	// edit details for db.
    	$this->PDO = new PDO('mysql:dbname='.''.';host='.'',"","");
       
    }
	
	

    /**
     * connected
     * 
     * @access private
     *
     * @return mixed Value.
     */
	private function connected(){
		if(!$this->PDO){
			return false;
		}		
		return true;		
	}


    /**
     * kill
     * 
     * @uses kills connection.
     * 
     * @access public
     *
     * @return nothing.
     */
	function kill(){
		if($this->PDO){
			$this->PDO = null;
		}
	}


    /**
     * registerUser
     * 
     * @param mixed $username user's full name.
     * @param mixed $email    user's email.
     * @param mixed $password user's password.
     * @param mixed $student_number user's student card's number.
     * @param bool  $admin    if user is admin.(0/1-F/T)
     *f
     * @access public
     *
     * @return id of the user in an associative array if registered successfully.
     */

	function registerUser($username, $email, $password, $student_number, $admin=0){
		$password = md5($password);
		$admin = (int)$admin;

		$query = $this->PDO->prepare("INSERT INTO users (username, email, password, student_number, admin) VALUE (:username, :email, :password, :student_number, :admin)");
		$query->bindParam(":username", $username, PDO::PARAM_STR);
		$query->bindParam(":email", $email, PDO::PARAM_STR);
		$query->bindParam(":password", $password, PDO::PARAM_STR);
		$query->bindParam(":admin", $admin, PDO::PARAM_INT);
        $query->bindParam(":student_number", $student_number, PDO::PARAM_INT);

		$result = $query->execute();

		if(!$result){ return false; }

        return array('id' => $this->PDO->lastInsertId());
	}

    

    /**
     * isRegistered
     * 
     * @uses checks if the email entered is already in the database.
     * 
     * @param mixed $email user's email.
     *
     * @access public
     *
     * @return true if there is ONLY 1 row found in the databse with that email.
     */
	function isRegistered($email){
		$query = $this->PDO->prepare("SELECT COUNT('id') FROM users WHERE email=:email");
		$query->bindParam(":email", $email, PDO::PARAM_STR);
		$query->execute();
		
		return ($query->rowCount() === 1)?true:false;
	}

	

    /**
     * login
     *
     * @uses checks if email and password provided matches a user in the database.
     * 
     * @param mixed $email    user's email.
     * @param mixed $password user's password.
     *
     * @access public
     *
     * @return the id of that user if the email and password provided match.
     */
	function login($email, $password){
           $password = md5($password);

           $query = $this->PDO->prepare("SELECT id, admin FROM users WHERE email = :email AND password = :password");
           $query->bindParam(":email", $email, PDO::PARAM_STR);
            $query->bindParam(":password", $password, PDO::PARAM_STR);

            $query->execute();
            $row_count = $query->rowCount();
            $row_count = (int)$row_count;

            if($row_count === 1){
            $row = $query->fetch(PDO::FETCH_ASSOC);

            //$json = '	';

            //var_dump(json_decode($json));

            return $row;

            //var_dump(json_decode($json, true));
            }else{
            return false;
            }
       }

    /**
     * getUser
     * 
     * @uses retrieve a single user's details.
     *
     * @param mixed $id user id.
     *
     * @access public
     *
     * @return complete details for the user in an associative array.
     */
	function getUser($id){
		$id = (int)$id;
		$query = $this->PDO->prepare("SELECT id, username, email FROM users WHERE id = :id");
		$query->bindParam(":id", $id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result){
			return false;
		}

		return $query->fetch(PDO::FETCH_ASSOC);
	}

    /**
     * getUserList
     *
     * @uses retrieve details for all users in the database.
     * 
     * @access public
     *
     * @return array of arrays user details in an associative array.
     */
	function getUserList(){
		$query = $this->PDO->prepare("SELECT id, username, email FROM users");
		$query->bindParam(":id", $id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result){
			return false;
		}

		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

    /**
     * updateUser
     * 
     * @param mixed $user_id   user's id.
     * @param mixed $user_name user's new or old name.
     * @param mixed $email     user's new or old email.
     * @param mixed $password  user's new or old password.
     *
     * @access public
     *
     * @return true, on successful update.
     */
    function updateUser($user_id, $user_name, $email, $password){
        $password = md5($password);

        $query = $this->PDO->prepare("UPDATE users SET username = :user_name,email = :email, password = :password WHERE id = :id");
        $query->bindParam(":id", $user_id, PDO::PARAM_INT);
        $query->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":password", $password, PDO::PARAM_STR);

        $result = $query->execute();
        if(!$result)    return false;
        return true;
    }


    /**
     * deleteUser
     * 
     * @param mixed $id user's id to be removed from the database.
     *
     * @access public
     *
     * @return true, on successful removal.
     */
    function deleteUser($id){
        $query = $this->PDO->prepare("DELETE FROM users WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;
        return true;
    }

    /**
     * createEvent
     * 
     * @param mixed $creator_user_id the admin_id who created the event.
     * @param mixed $title           event's title.
     * @param mixed $description     event's description.
     * @param mixed $location        event's location.
     * @param mixed $start_date      event's starting date.
     * @param mixed $end_date        event's end date.
     * @param bool  $is_free         if the event is free, or not.(0/1-F/T)
     * @param int   $price           default set to 0. If not needed, no need to enter when method is call.
     *
     * @access public
     *
     * @return id for the created event, in an associative array.
     */
    // function createEvent($creator_user_id, $title, $description, $location, $start_date, $end_date, $is_free, $price=0){
    //     $query = $this->PDO->prepare("INSERT INTO events (title, description, location, start_date, end_date, is_free, price, user_id) VALUES (:title, :description, :location, :start_date, :end_date, :is_free, :price,:user_id)");
    //     $query->bindParam(":title", $title, PDO::PARAM_STR);
    //     $query->bindParam(":description", $description, PDO::PARAM_STR);
    //     $query->bindParam(":location", $location, PDO::PARAM_STR);
    //     $query->bindParam(":start_date", $start_date, PDO::PARAM_STR);
    //     $query->bindParam(":end_date", $end_date, PDO::PARAM_STR);
    //     $query->bindParam(":is_free", $is_free, PDO::PARAM_INT);
    //     $query->bindParam(":price",$price, PDO::PARAM_STR);
    //     $query->bindParam(":user_id", $creator_user_id, PDO::PARAM_INT);

    //     $result = $query->execute();
    //     if(!$result)    return false;

    //     return array('id' => $this->PDO->lastInsertId());
    // }
    //new version
    function createEvent($creator_user_id, $title, $description, $timetable_id, $is_free, $price=0){
        $query = $this->PDO->prepare("INSERT INTO events (title, description, timetable_id,  is_free, price, user_id) VALUES (:title, :description, :timetable_id, :is_free, :price,:user_id)");
        $query->bindParam(":title", $title, PDO::PARAM_STR);
        $query->bindParam(":description", $description, PDO::PARAM_STR);
        $query->bindParam(":is_free", $is_free, PDO::PARAM_INT);
        $query->bindParam(":price",$price, PDO::PARAM_STR);
        $query->bindParam(":timetable_id", $timetable_id, PDO::PARAM_INT);
        $query->bindParam(":user_id", $creator_user_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        return array('id' => $this->PDO->lastInsertId());
    }

    /**
     * getEvent
     * 
     * @param mixed $id event's id to get details of.
     *
     * @access public
     *
     * @return details of the event, in an associative array.
     */
    function getEvent($id){
        $query = $this->PDO->prepare("SELECT events.id,events.title, events.description, events.is_free,events.price, CONCAT(rooms.name,' ',rooms.number) AS location, timetable.start_date, timetable.end_date FROM events LEFT JOIN timetable ON events.timetable_id = timetable.id LEFT JOIN rooms ON timetable.room_id = rooms.id WHERE events.id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        $row_count = $query->rowCount();
        $row_count = (int)$row_count;

        if($row_count !== 1)    return false;
        
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    

    /**
     * getEventList
     * 
     * @access public
     *
     * @return details of all available events in an associative array.
     */
    function getEventList(){
        $query = $this->PDO->prepare("SELECT events.id,events.title, events.description, events.price, CONCAT(rooms.name,' ',rooms.number) AS location, timetable.start_date, timetable.end_date FROM events LEFT JOIN timetable ON events.timetable_id = timetable.id LEFT JOIN rooms ON timetable.room_id = rooms.id");

        $result = $query->execute();
        if(!$result)    return false;

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * deleteEvent
     * 
     * @param mixed $id id of the event to be removed from the database.
     *
     * @access public
     *
     * @return true on successful removal.
     */
    function deleteEvent($id){
        $query = $this->PDO->prepare("DELETE FROM events WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        return true;
    }

    /**
     * updateEvent
     * 
     * @param mixed     $id          event's id to be updated.
     * @param mixed     $title       event's title
     * @param mixed     $description event's description.
     * @param mixed     $location    event's location.
     * @param mixed     $start_date  event's start_date.
     * @param mixed     $end_date    event's end_date.
     * @param int/bool  $is_free     if event is free.
     * @param float     $price       event's fee.
     *
     * @access public
     *
     * @return true on successful update.
     */
    // function updateEvent($id, $title, $description, $location, $start_date, $end_date, $is_free, $price=0){
    //  // echo $id .', '.$title.', '.$description.', '.$location.', '.$start_date.', '.$end_date.', '.$is_free.', '.$price;
    //     $query = $this->PDO->prepare("UPDATE events SET title = :title, description = :description, location = :location, start_date = :start_date, end_date = :end_date, is_free = :is_free, price = :price WHERE id = :id");
    //     $query->bindParam(":id", $id, PDO::PARAM_INT);
    //     $query->bindParam(":title", $title, PDO::PARAM_STR);
    //     $query->bindParam(":description", $description, PDO::PARAM_STR);
    //     $query->bindParam(":location", $location, PDO::PARAM_STR);
    //     $query->bindParam(":start_date", $start_date, PDO::PARAM_STR);
    //     $query->bindParam(":end_date", $end_date, PDO::PARAM_STR);
    //     $query->bindParam(":is_free", $is_free, PDO::PARAM_INT);
    //     $query->bindParam(":price", $price, PDO::PARAM_INT);

    //     $result = $query->execute();

    //     if(!$result)    return false;
    //     return true;
    // }

    //function updateEvent($id, $title, $description, $timetable_id, $is_free, $price=0){
    function updateEvent($id, $title, $description, $is_free, $price = 0){ 
     // echo $id .', '.$title.', '.$description.', '.$location.', '.$start_date.', '.$end_date.', '.$is_free.', '.$price;
        $query = $this->PDO->prepare("UPDATE events SET title = :title, description = :description, is_free = :is_free, price = :price WHERE id = :id");
       //$query = $this->PDO->prepare("UPDATE events LEFT JOIN timetable ON events.timetable_id = timetable.id LEFT JOIN rooms ON timetable.room_id = rooms.id SET events.title = :title, events.description = :description, ")
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->bindParam(":title", $title, PDO::PARAM_STR);
        $query->bindParam(":description", $description, PDO::PARAM_STR);
        //$query->bindParam(":timetable_id", $timetable_id, PDO::PARAM_INT);
        $query->bindParam(":is_free", $is_free, PDO::PARAM_INT);
        $query->bindParam(":price", $price, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result)    return false;
        return true;
    }

    /**
     * getEventUserList
     * 
     * @uses retrieves users attending for the given event.
     * 
     * @param mixed $event_id event's id.
     *
     * @access public
     *
     * @return user's id and username in an associative array.
     */
    function getEventUserList($event_id){
        $query = $this->PDO->prepare("SELECT user.id, user.username FROM users AS user LEFT JOIN attenders AS attender ON user.id = attender.user_id WHERE attender.event_id = :event_id");
        $query->bindParam(":event_id", $event_id, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result)    return false;
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * createComment
     * 
     * @param mixed $user_id  user's id.
     * @param mixed $event_id event's id.
     * @param mixed $content  comment.
     * @param mixed $date     date the comment has been made.
     *
     * @access public
     *
     * @return id of the comment.
     * @return false if there is an error.
     */
    function createComment($user_id, $event_id, $content){
        $date = ''.date('Y-m-d H:i:s'); 
        $query = $this->PDO->prepare("INSERT INTO comments (user_id, event_id, content, post_date) VALUES (:user_id, :event_id, :content, :post_date)");
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->bindParam(":event_id",$event_id, PDO::PARAM_INT);
        $query->bindParam(":content", $content, PDO::PARAM_STR);
        $query->bindParam(":post_date", $date, PDO::PARAM_STR);

        $result = $query->execute();

        if(!$result)    return false;

        return array('id' => $this->PDO->lastInsertId());
    }

    /**
     * getCommentList
     * 
     * @uses retrieves comments made for the given event
     *
     * @param mixed $event_id event's id.
     *
     * @access public
     *
     * @return comment's id, content and creation date in an associative array.
     */
    function getCommentList($event_id){
        //$query = $this->PDO->prepare("SELECT user.id AS 'user_id', user.username AS 'username', comment.id AS 'comment_id', comment.content AS 'content', comment.post_date AS 'date' FROM comments AS 'comment' LEFT JOIN users AS 'user' ON user.id = comemnt.user_id WHERE event_id = :event_id"); 
        $query = $this->PDO->prepare("SELECT users.id AS 'user_id', users.username, comments.id AS 'comment_id', comments.content, comments.post_date FROM comments LEFT JOIN users ON users.id = comments.user_id WHERE comments.event_id= :event_id");
        $query->bindParam(":event_id", $event_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * deleteComment
     * 
     * @param mixed $comment_id comment's id.
     *
     * @access public
     *
     * @return true if the comment is deleted.
     * @return false if there is an error.
     */
    function deleteComment($comment_id){
        $query = $this->PDO->prepare("DELETE FROM comments WHERE id = :id");
        $query->bindParam(":id", $comment_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        return true;
    }	

    /**
     * getFeedbackList
     * 
     * @uses retrieves all the feedbacks available in the database.
     * 
     * @access public
     *
     * @return list of feedback information, DOES NOT return the content of the feedback.
     */
	function getFeedbackList(){
		$query = $this->PDO->prepare("SELECT id, title, sent_date FROM feedback");

		$result = $query->execute();
		if(!$result)      return false;

        return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	

    /**
     * getFeedBack
     * 
     * @uses retrieves single feedback information.
     * 
     * @param mixed $feedback_id feedback id.
     *
     * @access public
     *
     * @return associative array of the feedback details for the id given.
     * @return false if there is an error.
     */
	function getFeedBack($feedback_id){
		$query = $this->PDO->prepare("SELECT * FROM feedback WHERE id = :feedback_id ");
		$query->bindParam(":feedback_id", $feedback_id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result)	return false;

		$row_count = $query->rowCount();
		$row_count = (int)$row_count;


		if($row_count === 1){
			return $query->fetch(PDO::FETCH_ASSOC);
		}

		return false; 
		
		//SELECT  reply.id AS response_id, reply.user_id AS responder_id,  reply.content AS response_content, reply.sent_date, main.title AS feedback_title,main.id AS thread_id FROM feedback AS main LEFT JOIN feedback AS reply ON main.id = reply.reply_id WHERE reply.reply_id = 1
	}

	

	

    /**
     * createFeedback
     * 
     * @param mixed $user_id user's id.
     * @param mixed $title   feedback title.
     * @param mixed $content feedback content.
     *
     * @access public
     *
     * @return id of the feedback if it is succeeded.
     * @return false if there is an error.
     */
	function createFeedback($user_id, $title, $content){
		$query = $this->PDO->prepare("INSERT INTO  feedback (user_id,  title, content, sent_date) VALUES (:user_id, :title, :content, CURDATE())");
		$query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$query->bindParam(':title', $title, PDO::PARAM_STR);
		$query->bindParam(':content', $content, PDO::PARAM_STR);

		$result->$query->execute();	
		if(!$result)	return false;

		return $this->PDO->lastInsertId();
	}

    //@TODO improve SELECT SQLs for messages. add sender and receiver names by joining user tables.
    //@TODO finish commenting createMessage
    function createMessage($user_id, $receiver_id, $title, $content){
        $query = $this->PDO->prepare("INSERT INTO messages (sender_id, receiver_id, title, content) VALUES (:user_id, :receiver_id, :title, :content)");
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->bindParam(":receiver_id", $receiver_id, PDO::PARAM_INT);
        $query->bindParam(":title", $title, PDO::PARAM_STR);
        $query->bindParam(":content", $content, PDO::PARAM_STR);

        $result = $query->execute();
        if(!$result)     return false;

        return $this->PDO->lastInsertId();
    }

    //@TODO finish commenting getMessage
    function getMessage($id){
        $query = $this->PDO->prepare("SELECT sender_id, receiver_id, title, content, sent_date FROM messages WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result){
            return false;
        }
        return $query->fetch(PDO::FETCH_ASSOC);
    }

     //@TODO finish commenting getMessageList
    function getMessageList($user_id){
        $query = $this->PDO->prepare("SELECT sender_id, receiver_id, title, content, sent_date FROM messages WHERE receiver_id= :user_id");
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result){
            return false;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
        
     //@TODO finish commenting getSentMessageList
    function getSentMessageList($user_id){
        $query = $this->PDO->prepare("SELECT sender_id, receiver_id, title, content, sent_date FROM messages WHERE sender_id = :user_id");
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result){
            return false;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteMessage(){
        //@TODO implement deleteMessages. maybe change database for messsages
    }

    function getRoom($id){
        //@TODO may not need
    }

    /**
     * getRoomList
     * 
     * @uses looks for availablity of rooms in the given period of time,
     *       also checking for the capacity of the room.
     *
     * @param $start_date starting date and time of the event.
     * @param $end_date   end date and time of the event.
     * @param $capacity   expected number of people for the event.
     *
     * @access public
     *
     * @return array of room ids, that are in ASCENDING order of capacity (first one in the array is the smallest match to the given capacity number)
     */
    function getAvailableRoomIds($start_date, $end_date, $capacity){
        //$query = $this->PDO->prepare("SELECT id FROM rooms WHERE capacity >= :capacity AND id NOT IN (SELECT timetable.room_id FROM timetable WHERE start_date >= :start_datetime AND start_date <= :end_datetime OR end_date >= :start_datetime AND end_date <= :end_datetime) ORDER BY capacity ASC");
        $query = $this->PDO->prepare("SELECT id FROM rooms WHERE capacity >= :capacity AND id NOT IN (SELECT timetable.room_id FROM timetable WHERE  (start_date BETWEEN  :start_datetime AND :end_datetime OR end_date BETWEEN  :start_datetime AND :end_datetime) OR ( :start_datetime BETWEEN start_date  AND end_date AND :end_datetime BETWEEN  start_date AND end_date))");
        //$query = $this->PDO->prepare("SELECT id FROM rooms WHERE capacity >= :capacity AND id NOT IN (SELECT timetable.room_id FROM timetable WHERE start_date >= :start_datetime AND start_date <= :end_datetime OR end_date >= :start_datetime AND end_date <= :end_datetime OR start_date BETWEEN :start_datetime AND :end_datetime OR end_date BETWEEN  :start_datetime AND :end_datetime)");
        $query->bindParam(":start_datetime", $start_date, PDO::PARAM_STR);
        $query->bindParam(":end_datetime", $end_date, PDO::PARAM_STR);
        $query->bindParam(":capacity", $capacity, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result){
            return false;
        }

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }


 //   SELECT id FROM rooms WHERE capacity >= 80 AND id NOT IN (SELECT timetable.room_id FROM timetable WHERE start_date >= '2013-12-18 00:00:00' AND start_date <= '2013-12-18 02:00:00' OR end_date >= '2013-12-18 00:00:00' AND end_date <= '2013-12-18 02:00:00'
 //  OR start_date BETWEEN  '2013-12-18 00:00:00' AND '2013-12-18 02:00:00' OR end_date BETWEEN  '2013-12-18 00:00:00' AND '2013-12-18 02:00:00'  )

  //  SELECT id FROM rooms WHERE capacity >= 80 AND id NOT IN (SELECT timetable.room_id FROM timetable WHERE start_date >= '2013-12-18 16:00:00' AND start_date <= '2013-12-18 21:00:00' OR end_date >= '2013-12-18 16:00:00' AND end_date <= '2013-12-18 21:00:00'
 //  OR start_date BETWEEN  '2013-12-18 16:00:00' AND '2013-12-18 21:00:00' OR end_date BETWEEN  '2013-12-18 16:00:00' AND '2013-12-18 21:00:00'  )

    function getRoomList($ids){
        $id_place_holders = implode(',', array_fill(0, count($ids), '?'));
        $query = $this->PDO->prepare("SELECT id, CONCAT(name,' ',number) AS name, capacity FROM rooms WHERE id IN ($id_place_holders) ORDER BY capacity ASC");
        
      
        $result = false;
        $data = array();
        // for($i = 0; $i < sizeof($ids); $i++){
        //     $intid = intval($ids[$i]);
        //     $query->bindParam(":id", $intid, PDO::PARAM_INT);
        //     $result = $query->execute();

        //     if(!$result){
        //         return false;
        //     }

            
        //     $data[] = $query->fetch(PDO::FETCH_ASSOC);
        // }
        
        $result = $query->execute($ids);
        if(!$result) return false;

         $data = $query->fetchAll(PDO::FETCH_ASSOC);
         //print_r($data);
        return $data;
    }

    function getTimetable($id){
        $query = $this->PDO->prepare("SELECT timetable.id, CONCAT(rooms.name, ' ' , rooms.number) AS name, timetable.start_date, timetable.end_date FROM timetable LEFT JOIN rooms ON rooms.id = timetable.room_id WHERE timetable.id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result){
            return false;
        }

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function createTimetable($room_id, $start_date, $end_date){
        $query = $this->PDO->prepare("INSERT INTO timetable (room_id, start_date, end_date, booked_by) VALUES (:room_id, :start_date, :end_date, 'ISS')");
        $query->bindParam(":room_id", $room_id, PDO::PARAM_INT);
        $query->bindParam(":start_date", $start_date, PDO::PARAM_STR);
        $query->bindParam(":end_date", $end_date, PDO::PARAM_STR);
        
        $result = $query->execute();
        if(!$result){
            return false;
        }

        return $this->PDO->lastInsertId();
    }


    function updateTimetable($id, $room_id, $start_date, $end_date){
        $query = $this->PDO->prepare("UPDATE timetable SET room_id = :room_id, start_date = :start_date, end_date = :end_date WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->bindParam(":room_id", $room_id, PDO::PARAM_INT);
        $query->bindParam(":start_date", $start_date, PDO::PARAM_STR);
        $query->bindParam(":end_date", $end_date, PDO::PARAM_STR);

        $result = $query->execute();
        if(!$result){
            return false;
        }

        return true;
    }

    function deleteTimetable($id){
        $query = $this->PDO->prepare("DELETE FROM timetable WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result){
            return false;
        }

        return true;
    }





    /**
     * createPoll
     * 
     * @param mixed $question question for the poll.
     *
     * @access public
     *
     * @return poll id.
     */
	function createPoll($question){
		$query = $this->PDO->prepare("INSERT INTO polls (question) VALUES (:question)");
		$query->bindParam(":question", $question, PDO::PARAM_STR);

		$result = $query->execute();
		if(!$result)	return false;

		return $this->PDO->lastInsertId();
	}

    /**
     * createAnswer
     * 
     * @param $poll_id poll's id .
     * @param $answers answer.
     *
     * @access public
     *
     * @return id for the answer created.
     */
	function createAnswer($poll_id, $answer){
		$query = $this->PDO->prepare("INSERT INTO answers (poll_id, answer) VALUES (:poll_id, :answer)");
		$query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);
		$query->bindParam(":answer", $answer, PDO::PARAM_STR);

		$result = $query->execute();
		if(!$result)	return false;

		return $this->PDO->lastInsertId();
	}



    /**
     * votePoll
     *
     * @uses registers the given user voting for in a given poll.
     * @uses ***Use this right before incAnswerCount method, DO NOT use one without the other.
     * 
     * @param int $user_id user's id.
     * @param int $poll_id poll's id.
     *
     * @access public
     *
     * @return false.
     * @return id for the voting. 
     */
	function votePoll($user_id, $poll_id){
		$query = $this->PDO->prepare("INSERT INTO votes (user_id, poll_id) VALUES (:user_id, :poll_id)");
		$query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result)	return false;

		return $this->PDO->lastInsertId();
	}

    // function deleteVotePoll($user_id, $poll_id){
    //     $query = $this->PDO->prepare("DELETE FROM votes WHERE user_id = :user_id AND poll_id = :poll_id");
    //     $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    //     $query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);

    //     $result = $query->execute();
    //     if(!$result) return false;

    //     return true;
    // }

    /**
     * incAnswerCount
     *
     * @uses increaments the count for a given answer by 1.
     * @uses ***Use this right after votePoll method, DO NOT use one without the other.
     * 
     * @param mixed $answer_id id of the answer.
     *
     * @access public
     *
     * @return false, if it is interupted. 
     * @TODO NEED TESTING : incAnswerCount method <-db.php
     */
	function incAnswerCount($answer_id){
		$query = $this->PDO->prepare("SELECT * FROM answers WHERE id = :answer_id FOR UPDATE");
		$query->bindParam(":answer_id", $answer_id, PDO::PARAM_INT);

		$start = $this->PDO->beginTransaction();
		if($start === true){
			$result = $query->execute();

			if(!$result){
				$this->PDO->rollBack();
				return false;
			}

			$answer = $query->fetch(PDO::FETCH_ASSOC);
			$new_count = ((int)$answer['count']) + 1;

			$query = $this->PDO->prepare("UPDATE answers SET count = :new_count WHERE id = :answer_id");
			$query->bindParam(":new_count", $new_count, PDO::PARAM_INT);
			$query->bindParam(":answer_id", $answer_id, PDO::PARAM_INT);

			$result = $query->execute();

			if(!$result)	return $this->PDO->rollBack();

			$this->PDO->commit();

		}else{
			$this->PDO->rollBack();
		}
	}

    /**
     * canVotePoll
     *
     * @uses use before votePoll method, to prevent duplicate entries from same user. 
     * 
     * @param mixed $user_id user's id.
     * @param mixed $poll_id poll's id.
     *
     * @access public
     *
     * @return false, if the user has already voted for the given poll.
     * @return true, if the user can vote for the given poll.
     */
	function canVotePoll($user_id, $poll_id){
		$query = $this->PDO->prepare("SELECT * FROM votes WHERE user_id = :user_id AND poll_id = :poll_id");
		$query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);

		$result = $query->execute();

		$row_count = $query->rowCount();
		$row_count = (int)$row_count;

		if($row_count === 1)	return false;

		return true;
	}


    /**
     * getPolls
     * 
     * @access public
     *
     * @return an array of poll id, question, and state of poll, for all polls.
     */
	function getPollList(){
		//$query = $this->PDO->prepare("SELECT polls.id AS 'poll_id', polls.question, answers.id AS 'answer_id', answers.answer, answers.count FROM `answers` JOIN polls ON answers.poll_id = polls.id WHERE polls_id = :poll_id");
		$query = $this->PDO->prepare("SELECT * FROM polls WHERE state = 1");

		$result = $query->execute();

		if(!$result)	return false;

        return $query->fetchAll(PDO::FETCH_ASSOC);
	}

    function getPoll($id){
        $query = $this->PDO->prepare("SELECT * FROM polls WHERE state = 1 AND id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result)    return false;

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * setPollActive
     * 
     * @uses sets given poll's "status" to true/active
     *  
     * @param mixed $poll_id poll's id.
     *
     * @access public
     *
     * @return mixed true/false.
     */
	function setPollActive($poll_id){
		$query = $this->PDO->prepare("UPDATE polls SET state = 1 WHERE poll_id = :poll_id");
		$query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result)	return false;

		return true;
	}


    /**
     * setPollDeactive
     * 
     * @uses sets given poll's "status" to false/inactive
     * 
     * @param mixed $poll_id Description.
     *
     * @access public
     *
     * @return mixed true/false.
     */
	function setPollDeactive($poll_id){
		$query = $this->PDO->prepare("UPDATE polls SET state = 0 WHERE poll_id = :poll_id");
		$query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result)	return false;

		return true;
	}	


    function getPollAnswerList($poll_id){
        $query = $this->PDO->prepare("SELECT * FROM answers WHERE poll_id = :poll_id");
        $query->bindParam(":poll_id", $poll_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    

    /**
     * createAttender
     * 
     * @uses checks if the user is already listed as attending for, if not proceeds to list user as attending.
     * @uses isAttendingEvent($user_id, $event_id) is used.
     *
     * @param mixed 	$user_id  user's id for the user who is attending an event.
     * @param mixed 	$event_id event's id for the event the user is atteding.
     * @param int/bool  $paid     Default is True, if not specified.
     *
     * @access public
     *
     * @return mixed true/false.
     */
	function createAttender($user_id, $event_id, $paid=1){
		if($this->checkAttender($user_id,$event_id) === true) return false;
		$query = $this->PDO->prepare("INSERT INTO attenders (user_id, event_id, paid) VALUES (:user_id, :event_id, :paid)");
		$query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$query->bindParam(":event_id", $event_id, PDO::PARAM_INT);
		$query->bindParam(":paid", $paid, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result)	return false;

		return true;
	}

    /**
     * checkAttender
     * 
     * @uses to check if the user is already attending an event.
     * 
     * @param mixed $user_id  user'id.
     * @param mixed $event_id event's id.
     *
     * @access public
     *
     * @return true if there is already a single row for that user.
     * @return false if there is more than 1 or no row for that user.
     */
	function checkAttender($user_id, $event_id){
		$query = $this->PDO->prepare("SELECT * FROM attenders WHERE user_id = :user_id AND event_id = :event_id");
		$query->bindParam("user_id", $user_id, PDO::PARAM_INT);
		$query->bindParam("event_id", $event_id, PDO::PARAM_INT);

		$result = $query->execute();
		if(!$result)	return false;

		$row_count = $query->rowCount();
		$row_count = (int)$row_count;
		
		if($row_count === 1) return true;

		return false;
	}

    function deleteAttender($id){
        $query = $this->PDO->prepare("DELETE FROM attenders WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;

        return true;
    }

    function updateAttender($id, $paid){
        $query = $this->PDO->prepare("UPDATE attenders SET paid = :paid WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->bindParam(":paid", $paid, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result)    return false;
        return true;
    }

    


    function createAlbum($event_id, $title, $date_created){ 
        //@TODO FINISH createAlbum METHOD. <-db.php
    }

    function addPicToAlbum($album_id, $path){
        //@TODO FINISH addPicToAlbum METHOD. <-db.php
    }

    function getEventCommentList($event_id){
        //@TODO FINISH getCommentsForEvent METHOD. <-db.php
        $query = $this->PDO->prepare("SELECT id, user_id, event_id, content, post_date FROM comments WHERE event_id= :event_id");
        $query->bindParam(":event_id", $event_id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result){
            return false;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);        
    }

    function getLinkList(){
        $query = $this->PDO->prepare("SELECT * FROM links");

        $result = $query->execute();

        if(!$result){
            return false;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getLink($id){
        $query = $this->PDO->prepare("SELECT * FROM links WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result){
            return false;
        }

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function createLink($title, $description, $link){
        $query = $this->PDO->prepare("INSERT INTO links (title, description, link) VALUES(:title, :description, :link)");
        $query->bindParam(":title", $title, PDO::PARAM_STR);
        $query->bindParam(":description", $description, PDO::PARAM_STR);
        $query->bindParam(":link", $link, PDO::PARAM_STR);

        $result = $query->execute();

        if(!$result){
            return false;
        }

        return $this->PDO->lastInsertId();
    }

    function updateLink($id, $title, $description, $link){
        $query = $this->PDO->prepare("UPDATE links SET title = :title, description = :description , link = :link WHERE id = :id");
        $query->bindParam(":title", $title, PDO::PARAM_STR);
        $query->bindParam(":description", $description, PDO::PARAM_STR);
        $query->bindParam(":link", $link, PDO::PARAM_STR);
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result){
            return false;
        }

        return true;
    }

    function deleteLink($id){
        $query = $this->PDO->prepare("DELETE FROM links WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        $result = $query->execute();
        if(!$result){
            return false;
        }

        return true;
    }



    ///TRIGGER

//    CREATE TRIGGER auto_timetable_deletion AFTER DELETE on events
//    FOR EACH ROW
//    BEGIN
//    DELETE FROM timetable
//        WHERE timetable.id = old.timetable_id;
//    END


	

    //@TODO Budget List db, db methods, controlelrs.
    //@TODO Finish off commenting.
}
?>