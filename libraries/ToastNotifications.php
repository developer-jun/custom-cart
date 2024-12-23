<?php

namespace Libraries;

use \Models\Notification;

class ToastNotifications {
    protected $notification;

    public function __construct(string $title = '', string $content = '') {
        $this->notification = new Notification($title, $content);
    }

    public function toastNotification(string $title = '', string $content = ''): string {
        if($title != '' && $content != '') {
            $this->notification = new Notification($title, $content);
        }
        ob_start();
        ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toastTarget" class="toast" role="alert" data-bs-delay="30000" aria-live="assertive" aria-atomic="true">
                <div class="toast-header" style="justify-content: space-between">                
                    <strong class="message-title"><?= $this->notification->title ?></strong>
                    
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"><?= $this->notification->content ?></div>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}