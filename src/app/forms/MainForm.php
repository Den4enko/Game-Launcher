<?php
namespace app\forms;

use php\compress\ZipFile;
use php\lib\fs;
use php\gui\UXData;
use php\lib\str;
use php\gui\UXDialog;
use php\gui\framework\AbstractForm;
use php\gui\event\UXEvent; 
use php\gui\event\UXWindowEvent; 


class MainForm extends AbstractForm
{

    /**
     * @event downloadButton.action 
     */
    function doDownloadButtonAction(UXEvent $event = null)
    {
        $url = $this->urlEdit->text;
            
        if (!str::startsWith($url, 'http://') and !str::startsWith($url, 'https://')) {
            UXDialog::showAndWait('Введите корректный url', 'ERROR');
            return;
        }
    
          { fs::makeDir('temp');
           $this->downloader->destDirectory = "temp/";  
            $this->downloader->urls = $this->urlEdit->text;
            $this->downloader->start();
            
            $this->panel->enabled = true;
            $this->downloadButton->enabled = false;
            
            $this->fileNameLabel->text = "Подождите...";
            $this->fileNameLabel->show();

        }
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



}
