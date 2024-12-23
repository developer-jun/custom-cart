<?php

namespace Views;

class PageView {

    public array $scripts; // inline
    public array $scripts_includes; // file

    public array $styles; // inline
    public array $styles_includes; // file


    public function addScript(string $script = '') {
        if($script) {
            $this->scripts[] = $script;
        }
    }

    public function addScriptFile(string $file = '') {
        if($file) {
            $this->scripts_includes[] = $file;
        }
    }

    public function addStyle(string $style = '') {
        if($style) {
            $this->styles[] = $style;
        }
    }

    public function addStyleFile(string $style = '') {
        if($style) {
            $this->styles_includes[] = $style;
        }
    }

    public function includeScripts(): string {
        if(empty($this->scripts)) {
            return '';
        }
        ob_start();
        ?>
        <script>
        <?php
        foreach($this->scripts as $script) {
            echo $script ."\n";
        }
        ?>
        </script>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function includeScriptFiles(): string {
        if(empty($this->scripts_includes)) {
            return '';
        }
        ob_start();
        foreach($this->scripts_includes as $script) {
            ?>
            <script src="<?= $script ?>" defer></script>
            <?php
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function includeStyles(): string {
        if(empty($this->styles)) {
            return '';
        }        
        ob_start();
        ?>
        <style>
        <?php
        foreach($this->styles as $style) {
            echo $style ."\n";
        }
        ?>
        </style>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function includeStyleFiles(): string {
        if(empty($this->styles_includes)) {
            return '';
        }

        ob_start();
        foreach($this->styles_includes as $style) {
            ?>
            <link rel="stylesheet" href="<?= $style ?>">
            <?php
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

}