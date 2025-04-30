Templar.attribute('showIf',{
onChange : function(self, val){
    //this is a hack due to bug in templar
    if(typeof self.dataset['aplRepeat'] == 'undefined'){
        if(val === true || val == 'true' || parseInt(val) > 0){
            self.style.display = '';
        }else{
            self.style.display = 'none';
        }
    }
    
}
});

