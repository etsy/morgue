<?php

class SlackMessageFormatter {

    private $userList;
    private $message;

    /**
     * SlackMessageFormatter constructor.
     * @param $userList Slack User List
     */
    public function __construct($userList) {
        $this->userList = $userList;
    }

    /**
     * @return mixed
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }


    public function userName() {
        // Change Username: <@U38A3DE9> into user_name
        $this->message = preg_replace_callback('#<@(.*?)>#s',
            function ($matches) {
                $userId = $matches[1];
                return '<span class="annotated-user">@'.$this->userList[$userId]->real_name.'</span>';
            },
            $this->message);
        return $this;
    }

    public function channelName() {
        // Change Channelname: <#C1N8J0BTR|channel_name> into #channel_name
        $this->message = preg_replace_callback('/<#(.*?)>/s',
            function ($matches) {
                $arr = explode('|', $matches[1]);
                return '<span class="annotated-channel">#'.$arr[1].'</span>';
            },
            $this->message);
        return $this;
    }

    public function teamName() {
        // Change Teamname: <!subteam^P2V8M2WRR|@team_team> into @team_team
        $this->message = preg_replace_callback('/<!(.*?)>/s',
            function ($matches) {
                $str = $matches[1];
                if (stripos($str, '|') !== false) {
                    $arr = explode('|', $str);
                    $str = $arr[1];
                }
                return '<span class="annotated-team">'.$str.'</span>';
            },
            $this->message);
        return $this;
    }

    public function code() {
        // Change ``` into code format
        $this->message = preg_replace_callback('#```(.*?)```#s',
            function ($matches) {
                return '<pre>'.$matches[1].'</pre>';
            },
            $this->message);
        return $this;
    }

    public function anchorTag() {
        // Change <http://url> into link
        $this->message = preg_replace_callback('#<http(.*?)>#s',
            function ($matches) {
                $str = $matches[1];
                if (stripos($str, '|') !== false) {
                    $arr = explode('|', $str);
                    $str = $arr[0];
                }
                return '<a href="http' . $str . '">http' . $str . '</a>';
            },
            $this->message);
        return $this;
    }


    public function messageDiv( $userId, $messageText, $messageDateTime) {
        $returnStr = '<div><img src="' . $this->userList[$userId]->profile->image_72 . '" /><div class="message">';
        $returnStr.= '<div class="username">'. $this->userList[$userId]->name . '</div><div class="time">' . $messageDateTime . '</div>';
        $returnStr.= '<div class="msg">' . $messageText . "</div></div></div><br/>\n";
        return $returnStr;
    }

}
