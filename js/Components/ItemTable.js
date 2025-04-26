Templar.component('ItemTable',{
    templateURL : '/js/Components/ItemTable.html',
    attributes : {

        isvisible : function(self, val){
          var wrapper;
          if((wrapper = self.querySelector('[id=item-table-wrapper]')) != null){
            let banner = self.querySelector('[id=item-table-banner]')
            wrapper.setAttribute('showIf', val);
            banner.setAttribute('showIf', val);
          }
        },
    
        rowsource : function(self, val){
          var main;
          if((main = self.querySelector('[id=item-repeater-parent]') )!= null){
            main.dataset['aplRepeat'] = val;
          }
        }
      }
});