        /**
         * Cropping Manager
         */
        var CropImageManager = {

            /**
             * Holds the current Cropper.Img object
             * @var obj
             */
            curCrop: null,

            /**
             * Holds all the Ratio buttons
             * @var tab
             */
            sel_buttons: [],
            
            /**
             * Holds the selected ratio
             * @var string
             */
            selected: null,

            /**
             * Holds the selected button (toggle class)
             * @var obj
             */
            selectedButton: null,
            
            /**
             * Coords of the current cropping
             * @var {x1,y1,x2,y2,w,h}
             */
            coords: { x1: 0, y1: 0, x2: 0, y2: 0, w: 0, h: 0 },

            /**
             * Ratio X,Y 
             * @var {x, y}
             */
            ratio: 1, 
        
            /**
             *  Maximum display width
             *  @var maxWidth
             */
            maxWidth: 600,

            /**
             * Cropper.Img object CallBack function
             */
            onEndCrop: function ( coords, dimensions ) {
                CropImageManager.coords.x1 = Math.floor(coords.x1 * CropImageManager.ratio);
				CropImageManager.coords.y1 = Math.floor(coords.y1 * CropImageManager.ratio);
				CropImageManager.coords.x2 = Math.floor(coords.x2 * CropImageManager.ratio);
				CropImageManager.coords.y2 = Math.floor(coords.y2 * CropImageManager.ratio);
		
                CropImageManager.coords.w = Math.floor(dimensions.width * CropImageManager.ratio);
                CropImageManager.coords.h = Math.floor(dimensions.height * CropImageManager.ratio);
                $('cropped_coords').innerHTML = '(' + coords.x1 + ',' + coords.y1 + ') - ' +
                                              CropImageManager.coords.w + ' x ' +
                                              CropImageManager.coords.h + ' pixels';
            },

            /**
             * Undo the cropping
             */
            undo: function() {
                CropImageManager.removeCropper();
                CropImageManager.attachCropper();
            },

            clear: function() {
                if (CropImageManager.selected)
                {
                    var coords = CropImageManager.coords;
                    var name = CropImageManager.selected;
                    $(name+'-x1').value = 0;
                    $(name+'-x2').value = 0;
                    $(name+'-y1').value = 0;
                    $(name+'-y2').value = 0;
                }
                
                var oldratio = this.selected;
                this.selected = null;
                this.selectRatio(oldratio);

                this.undo();
            },
            
            /**
             * Save the current Cropping dimension in TextFields
             */
            set: function() {
                var coords = CropImageManager.coords;
                var name = CropImageManager.selected;
                if(coords.x1 != coords.x2 && coords.y1 != coords.y2) {
                    $(name+'-x1').value = coords.x1;
                    $(name+'-x2').value = coords.x2;
                    $(name+'-y1').value = coords.y1;
                    $(name+'-y2').value = coords.y2;
                }
                wcmMessage.info($I18N.RATIO_UPDATED, 1500);
            },

            /** 
             * Change image for the cropping
             */
            setImage: function(source) {
                this.undo();
                $( 'cropping_image' ).src = $( 'croppingbugfix' ).src = source;

                // Reset buttons class
                for(i = 0; i < this.sel_buttons.length ; i++) this.sel_buttons[i].className = 'notset';

                // Reset values
                $('cropping_ratios').select('input]').each(function(item){item.value=0;});

                this.changeImageRatio();
            },
            
            select: function(button, ratio) {
                if (ratio != this.selected)
                {
                    if (this.selectedButton != null)
                    {
                        this.selectedButton.className = 'ratio';
                    }
                    button.className = 'active';
                    this.selectedButton = button;   
                    $('cropping_image').focus();
                    this.selectRatio(ratio);
                }
            },

            /**
             * Select a given ratio
             */
            selectRatio: function(ratio) {
                if (ratio != this.selected)
                {
                    CropImageManager.undo();
                    CropImageManager.selected = ratio;
                    if (ratio != '')
                    {
                        var r = CropImageManager.parseRatio(ratio);
                        CropImageManager.setCoords(ratio);
                        CropImageManager.setRatio(r.x, r.y);
                    }
                }
            },
            
            /**
             * Change the coords of the current Cropping.img
             */
            setCoords: function(name) {
                var x1 = $(name+'-x1').value || 0;
                var y1 = $(name+'-y1').value || 0;
                var x2 = $(name+'-x2').value || 0;
                var y2 = $(name+'-y2').value || 0;
                this.curCrop.options.onloadCoords = { x1: x1*1/this.ratio, y1: y1*1/this.ratio, x2: x2*1/this.ratio, y2: y2*1/this.ratio };
                this.curCrop.options.displayOnInit = (x1 != x2 && y1 != y2);
            },

            /**
             * Change the ration of the current Cropping.img
             */
            setRatio: function(ratioX, ratioY) {
                this.curCrop.options.ratioDim = { x: ratioX, y: ratioY };
                var gcd = this.curCrop.getGCD( this.curCrop.options.ratioDim.x, this.curCrop.options.ratioDim.y );
                this.curCrop.ratioX = this.curCrop.options.ratioDim.x / gcd;
                this.curCrop.ratioY = this.curCrop.options.ratioDim.y / gcd;
                this.curCrop.setParams();
            },


            /**
             * Init all actions and the Cropping.img
             */
            init: function(source) {
                if (this.curCrop != null)
                {
                    this.curCrop.remove();
                    this.curCrop = null;
                }         
                $('cropped_image').update('<span style="position:relative; top:0px; left:0px;">' +
                                          '<img src="img/empty.gif" alt="Image" id="cropping_image" style="display:none;" />' +
                                          '</span><img src="img/empty.gif" alt="Image" id="croppingbugfix" style="visibility: hidden;" />');
                this.selected = null;
                this.selectedButton = null;
                this.coords = { x1: 0, y1: 0, x2: 0, y2: 0, w: 0, h: 0 };

                $('cropping_ratios').select('input').each(function(button) {
                    switch(button.className) {
                        case 'set':
                            button.onclick = CropImageManager.set;
                            break;
                        case 'undo':
                            button.onclick = CropImageManager.undo;
                            break;
                        case 'select':
                            button.onclick = CropImageManager.choose;
                            break;
                    }
                });
                this.setImage(source);
            },

            changeImageRatio: function() {
                var img = $('cropping_image');
                var loader = new Image();
                loader.onload = function() {
                    var cw = $('originalWidth').innerHTML = loader.width;
                    var ch = $('originalHeight').innerHTML = loader.height;
                    $('cropped_coords').innerHTML = loader.width + ' x ' + loader.height + ' pixels';

                    if(cw > this.maxWidth) {
                        this.ratio = cw / this.maxWidth;
                        img.style.width = $('croppingbugfix').style.width = this.maxWidth + 'px';
                        img.style.height = $('croppingbugfix').style.height = (ch / this.ratio)+'px';
                        var locked = $('cropped_image');
                        if(locked) {
                            $('cropped_infos').style.width = this.maxWidth + 'px';
                            locked.style.width = this.maxWidth + 'px';
                            locked.style.height = (ch / this.ratio) + 'px';
                        }
                    } else {
                        this.ratio = 1;
                        img.style.width = $('croppingbugfix').style.width = cw + 'px';
                        img.style.height = $('croppingbugfix').style.height = ch +'px';
                        var locked = $('cropped_image');
                        if(locked) {
                            $('cropped_infos').style.width = cw + 'px';
                            locked.style.width = cw + 'px';
                            locked.style.height = ch + 'px';
                        }
                    }   
                    img.style.display = 'block';
                }.bind(this);
                
                if(img.src != null && img.src != '')
                	loader.src = img.src;
                else
                	loader.src = 'img/empty.gif';
            },
            
            /**
             * Parse the ratio unique name and return the ration
             *  @param s String ('rationXxY')
             *  @return {x,y}
             */
            parseRatio: function(s) {
                resultat = s.match(/^ratio([0-9]*)x([0-9]*)$/i);
                if(resultat)
                    return {x: resultat[1], y: resultat[2]};
            },

            /**
             * verif if all cropping are done
             */
            verifAllDone: function() {
                for(i = 0 ; i < this.sel_buttons.length ; i++) {
                    if(this.sel_buttons[i].className != 'valid') return false;
                }
                return true;
            },

            /** 
             * Attaches/resets the image cropper
             *
             * @access private
             * @return void
             */
            attachCropper: function() {
                if( this.curCrop == null ) this.curCrop = new Cropper.Img( 'cropping_image', { onEndCrop: this.onEndCrop } );
                else this.curCrop.reset();
            },

            /**
             * Removes the cropper
             *
             * @access public
             * @return void
             */
            removeCropper: function() {
                if( this.curCrop != null ) {
                    this.curCrop.remove();
                }
            }
        };
