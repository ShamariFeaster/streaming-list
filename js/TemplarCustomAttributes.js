Templar.attribute('showIf',{
onChange : function(self, val){
    
    if(val === true || val == 'true' || parseInt(val) > 0){
        self.style.display = '';
    }else{
        self.style.display = 'none';
    }
}
});

