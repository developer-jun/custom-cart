<?php
namespace Models;

use \Models\Status;

class Notification{
    public Status $status;
    public $title;

    public function __construct($title = '', $message = '', $type = 'success') {
        $this->status = new Status($type, $message);
        $this->title  = $title;
    }

    public function toArray(): array {
        return array_merge($this->status->toArray(), [
            'title' => $this->title,
        ]);
    }

    public function notificationTag(bool $exclude_title = false): string {
        $message = $this->status->messageTag();
        return '
            <div class="alert alert-'. $this->status->type .' alert-dismissible fade show" role="alert">
                '. (!$exclude_title ? '<strong>'. $this->title .'</strong>' : '') .'
                '. $message .'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        ';
    }
}