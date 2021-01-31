<?php
namespace app\forms;

use action\Element;
use bundle\updater\UpdateMe;
use php\compress\ZipFile;
use php\lib\fs;
use php\gui\UXData;
use php\lib\str;
use php\gui\UXDialog;
use php\gui\framework\AbstractForm;
use php\gui\event\UXEvent; 
use php\gui\event\UXWindowEvent; 
use php\gui\event\UXMouseEvent; 


class MainForm extends AbstractForm
{

    }

    /**
     * @event stopButton.action 
     */
    function doStopButtonAction(UXEvent $event = null)
    {    
        if (UXDialog::confirm('Вы уверены, что хотите остановить скачивание?')) {
            $this->downloader->stop();
        }
    }




    /**
     * @event deleteButton.click 
     */
    function doDeleteButtonClick(UXMouseEvent $e = null)
    {
        $dir = 'game';
        fs::clean($dir);
        $this->fileNameLabel->visible = true;
        $this->fileNameLabel->text = "Очищено";
    }



    /**
     * @event startButton.click-Left 
     */
    function doStartButtonClickLeft(UXMouseEvent $e = null)
    {
        open("game/Game.jar", false);
        app()->shutdown();
    }

    /**
     * @event downloadButton.action 
     */
    function doDownloadButtonClick(UXEvent $e = null)
    {
                
              $this->fileNameLabel->text = "Создание временной папки...";
               fs::makeDir('temp');
               $this->downloader->destDirectory = "temp/"; 
                $this->downloader->urls = 'https://github.com/Den4enko/Internet-Game/releases/download/test/Game.zip';
                $this->downloader->start();
                
                $this->stopButton->enabled = true;
                $this->downloadButton->enabled = false;
                
                $this->fileNameLabel->text = "Подождите...";
                $this->fileNameLabel->show();
                
            
            
            }


    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
    UpdateMe::start(self::VERSION);
    if (fs::exists("game/ver.txt")) {
    Element::loadContentAsync($this->gameVer, "game/ver.txt", function () use ($e, $event) {});
    $this->downloadButton->visible = false;
    $this->startButton->visible = true;
    $this->optionsPanel->enabled = true;
} else { 
    Element::setText($this->gameVer, 'Игра не установлена');
    $this->downloadButton->text = "Установить";
    $this->downloadButton->visible = true;
        }
    $this->gameVer->visible = true;
    }
        
    
