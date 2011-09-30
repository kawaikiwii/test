<?php 
/**
 * Project:     WCM
 * File:        biz.distributionChannel.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

class distributionChannel extends wcmObject {
    public $code;
    public $exportRuleId;
    public $type; // [ftp|fs|email]
    
    /*
     * connexionString : serialized array
     *
     * FTP   :: array(
     *		'host'           => '192.168.0.55',
     *		'user'           => 'tn',
     *		'pass'           => 'tn',
     *		'remotePath_ftp' => '/IN/FILES/'.date('Y-m-d_His').'/'
     *		);
     * FS    :: array(
     *		'remotePath_fs' => WCM_DIR.'/business/TEMP/IN/FILES/'.date('Y-m-d_His').'/'
     *		);
     * EMAIL :: array(
     *		'fromName' => 'Sebastien Rodriguez',
     *		'fromMail' => 'srodriguez@eurocortex.fr',
     *		'to'       => 'tnivot@eurocortex.fr,tho78tlse@yahoo.fr',
     *		'title'    => 'Titre du mail'
     *		);
     */
    public $connexionString;
    public $active;
    
    private $email_encoding = 'UTF-8';
    private $currentExportRule;
    private $error = false;

    public function getError() {
        return $this->error;
    }

    protected function getDatabase() {
        if (!$this->database) {
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('exportRule')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__distributionChannel';
        }
    }

    public function setEmailEncoding($encoding) {
        $this->email_encoding = $encoding;
    }

    static function getTypeList() {
        return array('ftp'=>'FTP', 'fs'=>'FileSystem', 'email'=>'Email');
    }

    public function push($local_path = null, &$exportRule) {
        // Process
        $this->currentExportRule = &$exportRule;
        $method = $this->type.'_push';
        $this->$method($local_path);
        return $this->error;
    }

    private function ftp_push($local_path = null, $mode_pasv = true) {
        $params = unserialize($this->connexionString);
        $ftp_host = (isset($params['host'])) ? $params['host'] : '';
        $ftp_user = (isset($params['user'])) ? $params['user'] : '';
        $ftp_pass = (isset($params['pass'])) ? $params['pass'] : '';
        $remotePath_ftp = (isset($params['remotePath_ftp'])) ? $params['remotePath_ftp'] : '';
        
        @$exportruleParameters = $this->currentExportRule->getParameters();
        $remotePath_ftp = (isset($exportruleParameters['remotePath'])) ? $remotePath_ftp."/".$exportruleParameters['remotePath'] : $remotePath_ftp;
        
        $ftp_handle = ftp_connect($ftp_host);
        if ($ftp_handle) {
            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Distribution Channel '.$this->code.'('.$this->type.') : FTP connected]');
            
            if (ftp_login($ftp_handle, $ftp_user, $ftp_pass)) {
                $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [FTP logged]');

                // *** Ajout LSJ 10/09/10 *** : activation mode passif
                if (ftp_pasv($ftp_handle, true)) {
                        $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Passive mode activated !]');
                } else {
                        $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Error during activation of passive mode !]', 'error');
                }

                // **************************
                $currentRemotePath = '';
                if ($remotePath_ftp) {
                    $directories = explode('/', $remotePath_ftp);
                    foreach ($directories as $dirname) {
                        if ($dirname) {
                            $currentRemotePath .= $dirname.'/';
                            @ftp_mkdir($ftp_handle, $currentRemotePath);
                        }
                    }
                }
                if ($local_path) {
                    $this->ftp_push_datas($ftp_handle, $currentRemotePath, $local_path);
                }
            } else {
                $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Distribution Channel '.$this->code.'('.$this->type.') : FTP login failed]', 'error');
                $this->currentExportRule->setFatalError('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Distribution Channel '.$this->code.'('.$this->type.') : FTP login failed]');
                $this->error = true;
            }
            ftp_quit($ftp_handle);
            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Distribution Channel '.$this->code.'('.$this->type.') : FTP disconnected]');
        } else {
            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Distribution Channel '.$this->code.'('.$this->type.') : FTP connection failed]', 'error');
            $this->currentExportRule->setFatalError('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push [Distribution Channel '.$this->code.'('.$this->type.') : FTP connection failed]');            
            $this->error = true;
        }
    }

    private function ftp_push_datas($ftp_handle, $remotePath, $localPath) {
        @$exportruleParameters = $this->currentExportRule->getParameters();

        if (is_dir($localPath)) {
            if ($dir_handle = opendir($localPath)) {
                while (($file = readdir($dir_handle)) !== false) {
                    if (($file == '..') || ($file == '.'))
                        continue;
                        
                    if (is_file($localPath.'/'.$file) && strtolower(strrchr($localPath.'/'.$file, '.')) != ".xml") {
                        
                        if (!ftp_put($ftp_handle, $remotePath.'/'.$file, $localPath.'/'.$file, FTP_BINARY)) {
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file (not XML) :: '.$remotePath.$file.']', 'error');
                            $this->currentExportRule->setFatalError('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file (not XML) :: '.$remotePath.$file.']');
                            $this->error = true;                            
                        } 
                        else 
                        {
                        	// rajout de la gestion d'un fichier de confirmation lors du dépôt
                        	if (!empty($this->currentExportRule->confirmationFile))
                        	{
                        		$finalName = basename($file).".ok"; 
                        		$fp = fopen($localPath.'/'.$finalName,"w");
                        		fclose($fp);
								
                        		if (ftp_put($ftp_handle, $remotePath.'/'.$finalName, $localPath.'/'.$finalName, FTP_BINARY))
                        			$this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP confirmation file :: '.$remotePath.$finalName.' -> Transfer Done]');	
                        		else
                        			$this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP confirmation file :: '.$remotePath.$finalName.']', 'error');	
                        	}	 
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file (not XML):: '.$remotePath.$file.' -> Transfer Done]');
                        }
                    }
                    
                    if (is_dir($localPath.'/'.$file)) {
                        $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP make remote directory :: '.$remotePath.$file.'/]');
                        if (!ftp_mkdir($ftp_handle, $remotePath.'/'.$file)) {
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP make remote directory :: '.$remotePath.$file.'/ dir maybe exist ?]', 'warning');
                            //$this->error = true;
                        }
                        $this->ftp_push_datas($ftp_handle, $remotePath.'/'.$file.'/', $localPath.'/'.$file);
                    }
                }
                /* on bloque l'exécution, le temps dêtre sûr que les images sont bien déposées */
				$this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file :: Sleep 1 sec.]');
                usleep(1000000);
                rewinddir($dir_handle);
                
                while (($file = readdir($dir_handle)) !== false) {
                    if (($file == '..') || ($file == '.'))
                        continue;

                    

                    if (is_file($localPath.'/'.$file) && strtolower(strrchr($localPath.'/'.$file, '.')) == ".xml") {
                        $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file :: '.$remotePath.$file.']');
                        if (!ftp_put($ftp_handle, $remotePath.'/'.$file, $localPath.'/'.$file, FTP_BINARY)) {
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file :: '.$remotePath.$file.']', 'error');
                            $this->currentExportRule->setFatalError('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file :: '.$remotePath.$file.']');
                            $this->error = true;
                        } else 
                        {
                        	$this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::ftp_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FTP push file :: '.$remotePath.$file.']');
                        }
                    }
                    
                }
                
                closedir($dir_handle);
            }
        }
    }

    private function fs_push($local_path) {
        $params = unserialize($this->connexionString);
        $remotePath_fs = (isset($params['remotePath_fs'])) ? $params['remotePath_fs'] : '';
        
        @$exportruleParameters = $this->currentExportRule->getParameters();
        $remotePath_fs = (isset($exportruleParameters['remotePath'])) ? $remotePath_fs."/".$exportruleParameters['remotePath'] : $remotePath_fs;
        
        $currentRemotePath = '';
        if ($remotePath_fs) {
            $directories = explode('/', $remotePath_fs);
            foreach ($directories as $dirname) {
                if ($dirname) {
                    $currentRemotePath .= '/'.$dirname;
                    if (!is_dir($currentRemotePath))
                        mkdir($currentRemotePath);
                }
            }
        }
        if ($local_path) {
            $this->fs_push_datas($currentRemotePath, $local_path);
        }
    }


    private function fs_push_datas($remotePath, $localPath) {
        @$exportruleParameters = $this->currentExportRule->getParameters();
        if (is_dir($localPath)) {
            if ($dir_handle = opendir($localPath)) {
                while (($file = readdir($dir_handle)) !== false) {
                    if (($file == '..') || ($file == '.'))
                        continue;
                        
                    if (is_file($localPath.'/'.$file)) {
                        $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FS push file :: '.$remotePath.'/'.$file.']');
                        if (!copy($localPath.'/'.$file, $remotePath.'/'.$file)) {
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FS push file :: '.$remotePath.'/'.$file.']', 'error');
                            $this->error = true;
                        }
                        else
                        {
                        	// rajout de la gestion d'un fichier de confirmation lors du dépôt
                        	if (!empty($this->currentExportRule->confirmationFile))
                        	{
                        		$finalName = basename($file).".ok"; 
                        		$fp = fopen($remotePath.'/'.$finalName,"w");
                        		fclose($fp);
								$this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas ['.$remotePath.'/'.$finalName.' -> Confirmation Push OK]');
                        	}
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas ['.$remotePath.'/'.$file.' -> Push OK]');
                        }
                    }
                    
                    if (is_dir($localPath.'/'.$file)) {
                        $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FS make directory :: '.$remotePath.'/'.$file.']');
                        if (!mkdir($remotePath.'/'.$file)) {
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas [Distribution Channel '.$this->code.'('.$this->type.') : FS make directory :: '.$remotePath.'/'.$file.']', 'error');
                            $this->error = true;
                        }else{
                            $this->currentExportRule->logMsg('[t:'.$exportruleParameters['taskId'].'er:'.$this->exportRuleId.'dc:'.$this->id.'] bizDistributionChannel::fs_push_datas ['.$remotePath.'/'.$file.' -> Mkdir OK]');
                        }
                        $this->fs_push_datas($remotePath.'/'.$file, $localPath.'/'.$file);
                    }
                }
                closedir($dir_handle);
            }
        }
    }

    private function email_push($local_path) {
        // Contenu du mail dans fichier $local_path/index.xml
        
        require_once (WCM_DIR."/includes/mail/mail.php");
        
        $params = unserialize($this->connexionString);
        $fromName = (isset($params['fromName'])) ? $params['fromName'] : 'RELAXNEWS';
        $fromMail = (isset($params['fromMail'])) ? $params['fromMail'] : 'noreply@relaxnews.net';
        $to = (isset($params['to'])) ? $params['to'] : 'Default To';
        $title = (isset($params['title'])) ? $params['title'] : 'Default mail title';
        $mailContent = (isset($params['text'])) ? $params['text'] : 'Default mail content';
        
        $myMail = new htmlMimeMail();
        
        $myMail->setHeader('X-Mailer', 'HTML Mime mail class');
        $myMail->setHeader('Date', date('D, d M y H:i:s O'));
        $myMail->setFrom('"'.$fromName.'" <'.$fromMail.'>');
        $myMail->setSubject($title);
        
        
        if (isset($local_path)) {
            if (is_dir($local_path)) {
                if ($dir_handle = opendir($local_path)) {
                    while (($file = readdir($dir_handle)) !== false) {
                        if (($file == '..') || ($file == '.'))
                            continue;
                            
                        if (is_file($local_path.'/'.$file)) {
                            $handle = fopen($local_path.'/'.$file, "rb");
                            if ($file == 'index.xml') {
                                $this->currentExportRule->logMsg('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL set mail content :: '.$local_path.'/'.$file);
                                $mailContent = fread($handle, filesize($local_path.'/'.$file));
                            } else {
                                //$this->currentExportRule->logMsg('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL add Attachment :: '.$local_path.'/'.$file);
                                //$myMail->addAttachment(fread($handle, filesize($local_path.'/'.$file)), $file);
                            }
                            fclose($handle);
                        }
                    }
                    closedir($dir_handle);
                }
            }
        }
        
        
        $myMail->setHtmlCharset($this->email_encoding);
        $myMail->setHtml($mailContent);
        $myMail->setSMTPParams(SMTPServer, 25, ServerName, SMTPAuth, SMTPUser, SMTPPassword);
        $this->currentExportRule->logMsg('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL send mail to '.$to);
        $success = $myMail->send(explode(',', $to), 'smtp');
        
        if (isset($myMail->errors)) {
            foreach ($myMail->errors as $error)
                $this->currentExportRule->logMsg('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL send failed : '.$error, 'error');
            $this->currentExportRule->setFatalError('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL send failed');
            
            $this->error = true;
        }
        if (!$success) {
            $this->currentExportRule->logMsg('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL send failed', 'error');
            $this->currentExportRule->setFatalError('Distribution Channel '.$this->code.'('.$this->type.') : EMAIL send failed');
            $this->error = true;
        }
        unset($myMail);
    }
}
