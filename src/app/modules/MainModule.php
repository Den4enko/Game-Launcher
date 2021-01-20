<?php
namespace app\modules;

use php\compress\ZipFile;
use bundle\http\HttpResponse;
use php\gui\UXDialog;
use php\gui\framework\AbstractModule;
use php\gui\framework\ScriptEvent; 


class MainModule extends AbstractModule
{

    /**
     * @event downloader.progress 
     */
    function doDownloaderProgress(ScriptEvent $event = null)
    {    
        $percent = round($event->progress * 100 / $event->max, 2);
        
        $this->progressBar->progressK = $event->progress / $event->max;
        $this->speedLabel->text = round($this->downloader->speed / 1024) . " Kb/s";
        $this->speedLabel->show();
        
        $this->fileNameLabel->text ="" . $percent . "%";
        $this->fileNameLabel->show();
        
        $this->sizeLabel->text = round($event->max / 1024 / 1024, 2) . " Mb";
        $this->sizeLabel->show();
    }

    /**
     * @event downloader.done 
     */
    function doDownloaderDone(ScriptEvent $event = null)
    {    
        $this->panel->enabled = false;
        $this->downloadButton->enabled = true;
    }

    /**
     * @event downloader.errorOne 
     */
    function doDownloaderErrorOne(ScriptEvent $event = null)
    {    
        $message = $event->error ?: 'Неизвестная ошибка';
        
        /** @var HttpResponse $response */
        $response = $event->response;
        
        if ($response->isNotFound()) {
            $message = 'Файл не найден';
        } else if ($response->isAccessDenied()) {
            $message = 'Доступ запрещен';
        } else if ($response->isServerError()) {
            $message = 'Сервер недоступен';
        }
    
        UXDialog::showAndWait('Ошибка загрузки файла: ' . $message, 'ERROR');
    }

    /**
     * @event downloader.successAll 
     */
    function doDownloaderSuccessAll(ScriptEvent $event = null)
    {    
        $zipFile = new ZipFile('temp/game.zip');
        $zipFile->unpack('temp/');
    }

}
