/**

wcmLogWindow
Can be used to display messages, dot timers, and a progress bar

Log window id: <prefix>_logWindow
Progress bar id: <prefix>_progressBar
*/


var wcmLogWindow = Class.create({
    
    initialize: function(argLogFile, argIdPrefix, argUrl) {
        this.logFile = argLogFile;
        this.idPrefix = argIdPrefix;
        this.dotTimer = '';
        this.logWindow = $(this.idPrefix + '_logWindow');
        this.progressBar = $(this.idPrefix + '_progressBar');
        this.logList = $(this.idPrefix + '_logList');
        this.total = 0;
        this.percent = 0;
        this.currentPosition = 0;
        this.url = (argUrl != '')? wcmBaseURL + argUrl : wcmBaseURL + 'dialogs/logger.php';
        this.totalChecker = '';
        this.pid = 0;
    },
    
    error: function(argErrors)
    {
        this.errors = argErrors;
    },
    
    kill: function()
    {
        this.logReader.stop();
        this.logList.insert('CANCELLED');
        
        var me = this;
        
        d = Math.floor(Math.random() * 1001);
        new Ajax.Request(this.url + '?action=kill&pid=' + this.pid + '&logFile=' + this.logFile + '&date=' + d,
        {
            method: 'get',
            onComplete: function(response)
            {
                alert('Process (' + me.pid + ') killed');
            }
        });
    },
        
    
    getTotal: function()
    {
        var me = this;
        
        d = Math.floor(Math.random() * 1001);
        
        new Ajax.Request(this.url + '?action=getTotal&logFile=' + this.logFile + '&date=' + d,
        {
            method: 'get',
            onSuccess: function(response)
            {
                var json = response.responseText.evalJSON();
                
                if (json.error == 1)
                {
                    me.error(json.errorMsg);
                } else if (json.total > -1) {
                    me.total = json.total;
                    me.pid = json.pid;
                    me.totalChecker.stop();
                    me.killDotTimer();
                    $(me.idPrefix + '_count').insert(' <span>(' + me.total + ')</span>', {position: 'right'});
                    
                    if (me.total > 0)
                    {
                        me.logReader = new PeriodicalExecuter(function (pe)
                        {
                            me.checkLog();
                        },1);
                    }
                }
            }
        });
    },
    
    checkLog: function()
    {
        var me = this;
        
        d = Math.floor(Math.random() * 1001);
        new Ajax.Request(this.url + '?action=checkLog&logFile=' + this.logFile + '&d=' + d,
        {
            method: 'get',
            onSuccess: function(response)
            {
                var json = response.responseText.evalJSON();
                var total = parseInt(json.total);
                
                me.progressBar.setStyle({width: json.total + '%'});
                me.progressBar.update(json.total + '%');
                
                me.logList.insert(json.log);
                
                me.logWindow.scrollTop = me.logWindow.scrollHeight;
                
                if (json.error == 1)
                {
                    me.error(json.errors);
                }
                
                if (total >= 100)
                {
                    alert('Finished!');
                    me.stop();
                }
            }
        });
    },
    
    start: function()
    {
        // First calculate total number of messages
        var me = this;
        this.startDotTimer();
        this.totalChecker = new PeriodicalExecuter(function (pe)
        {
            me.getTotal();
        },1);
    },
    
    stop: function()
    {
        this.logReader.stop();
        this.logReader = '';
    },
    
    stopProcess: function()
    {
        new Ajax.Request(this.url + '?action=stop&logFile=' + this.logFile,
        {
            method: 'get',
            onSuccess: function(response)
            {
                var json = response.headerJSON;
                switch (json.status)
                {
                    case "error":
                        this.error(json.errorMsg);
                        break;
                    default:
                        alert('Process stopped');
                }
                this.stop();
            }
        }).bind(this);
    },
            
    
    startLogReader: function()
    {
        this.logReader = new PeriodicalExectuter(function(pe) {
            this.checkLog();
        }).bind(this);
    },
    
    dot: function()
    {
        $(this.idPrefix + '_count').insert('.');
    },
    
    startDotTimer: function()
    {
        var me = this;
        this.dotTimer = new PeriodicalExecuter(function(pe) {
            me.dot();
        }, 1);
    },
    
    killDotTimer: function(argTimerId)
    {
        this.dotTimer.stop();
        this.dotTimer = '';
    }
});