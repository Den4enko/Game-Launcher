<?php
namespace app\modules;

use bundle\updater\UpdateMe;
use action\Element;
use php\lib\fs;
use php\compress\ZipFile;
use bundle\http\HttpResponse;
use php\gui\UXDialog;
use php\gui\framework\AbstractModule;
use php\gui\framework\ScriptEvent; 


class MainModule extends AbstractModule
{
const VERSION = '0.0.2';
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
        $this->stopButton->enabled = false;
        $this->downloadButton->visible = false;
        $this->startButton->visible = true;
        $this->optionsPanel->enabled = true;
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
        $this->fileNameLabel->text = "Распаковка...";
        $zipFile = new ZipFile('temp/Game.zip');
        $zipFile->unpack('game/');
        $this->fileNameLabel->text = "Удаление ненужных файлов...";
        $dir = 'temp';
        
        $this->fileNameLabel->text = "Очищаем содержимое папки...";
        fs::clean($dir); // очищаем содержимое папки
        
        Element::loadContentAsync($this->gameVer, "game/ver.txt", function () use ($e, $event) {});
        $this->fileNameLabel->text = "Готово";
    }

    /**
     * @event construct 
     */
    function doConstruct(ScriptEvent $e = null)
    {    

    }

}
