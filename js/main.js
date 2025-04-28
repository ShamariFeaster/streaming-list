var _cache = {allMedia : Templar.getModel('Media').allMedia};
function filterTable() {
    
}

function applyFilter(platform = '', watched = '') {
    MediaModel = Templar.getModel('Media');
    MediaModel.allMedia = _cache.allMedia;
    MediaModel.filter('allMedia').using(function(item){
        return item.streaming_platform == platform;
    });
}

function resetFilters() {
    MediaModel = Templar.getModel('Media');
    MediaModel.allMedia = _cache.allMedia;
    
}

function toggleChildren(parentId) {
    //modify visibility in data model
    MediaModel = Templar.getModel('Media');
    let indexOfParent = MediaModel.allMedia.findIndex( (item) =>  item.id == parentId);
    if(indexOfParent > -1){
        let parentRow = MediaModel.allMedia[indexOfParent];
        let childrenVisibilityState = parentRow.showChildren;
        //toggle visibility of child rows
        parentRow.showChildren = !childrenVisibilityState;
        MediaModel.update('allMedia');
    }
    
}