<?php

class Conversation{
    public $id;
    public $message_count;
    public $created_at;
    public $messages = [];

    public static function make($dict){
        $message = new Conversation();
        $message->created_at = $dict['create_at'];
        $message->id = $dict['id'];
        $message->message_count = $dict['message_count'];

        return $message;
    }
}


class Message{
    public $sender;
    public $receiver;
    public $created_at;
    public $text;

    public static function make($dict){
        $message = new Message();
        $message->sender = $dict['sender'];
        $message->receiver = $dict['receiver'];
        $message->text = $dict['text'];
        $message->created_at = $dict['created_at'];

        return $message;
    }
}

class MessageManager{
    private $connection = null;

    function set_connection($conn){
        $this->connection = $conn;
    }

    function get_conversation($user_id, $conversation_id){
        if ($this->user_has_access_to_conversation($user_id, $conversation_id)) {
            $conversation = $this->get_conversation_by_id($conversation_id);
            $conversation-> messages = $this->get_messages_from_conversation($conversation_id);
            return $conversation;
        }
        return null;
    }

    function post_message($message, $conversation_id){
        if ($this->user_has_access_to_conversation($message->sender, $conversation_id) && $this->user_has_access_to_conversation($message->receiver, $conversation_id)){
            try{
                $this->add_message($conversation_id, $message);
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        return false;
    }

    function get_messages_from_conversation($conversation_id){
        $result = $this->connection->query("SELECT * FROM message where conversation_id = ".$conversation_id." order by created_at desc");
        $messages = array();
        while($row = $result->fetch_assoc()){
            $messages[] = Message::make($row);
        }
        return $messages;
    }

    private function user_has_access_to_conversation($user_id, $conversation_id) {
        $str = "SELECT * FROM message where conversation_id = " . $conversation_id . " and (sender = " . $user_id . " or receiver = " . $user_id . ")";
        $result = $this->connection->query($str);
        return $result->num_rows > 0;
    }

    private function get_conversation_by_id($conversation_id){
        $result = $this->connection->query("SELECT * FROM conversation where id = " . $conversation_id . " ");
        return Conversation::make($result->fetch_assoc());
    }

    private function add_message($conversation_id, $message){
        $conversation = $this->get_conversation_by_id($conversation_id);
        $this->connection->query("Update conversation set message_count = ".($conversation->message_count+1)." where id = " . $conversation_id . " ");
        $this->connection->query("insert into message(conversation_id, receiver, sender, text, created_at)
                VALUES (".$conversation_id.", ".$message->receiver. ", ".$message->sender.", '".$message->text."', NOW())");

    }
}
