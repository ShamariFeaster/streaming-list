let a =_upNextData[0];
_upNextData.push(a);
upNextRepeat = _upNextData;
Templar.dataModel('Media', {
    columns :[
        {displayName : 'Title', name : 'title', colWidth : '4'},
        {displayName : 'Type', name : 'type', colWidth : '1'},
        {displayName : 'Streaming Platform', name : 'streaming_platform', colWidth : '2'},
        {displayName : 'Watched', name : 'watched', colWidth : '1'},
        {displayName : 'Rating', name : 'rating', colWidth : '2'}, 
        {displayName : 'Next Airing', name : 'next_airing', colWidth : '2'}
    ],
    type : [
        {value : 'movie', text : 'Movie'},
        {value : 'tv_show', text : 'TV Show'},
        {value : 'franchise', text : 'Franchise'},
        {value : 'series', text : 'Series'}
    ],
    platform : [
        {value : 'AMC+', text : 'AMC+'},
        {value : 'AppleTV', text : 'AppleTV'},
        {value : 'Britbox', text : 'Britbox'},
        {value : 'Discovery+', text : 'Discovery+'},
        {value : 'Hulu', text : 'Hulu'},
        {value : 'MAX', text : 'MAX'},
        {value : 'MGM+', text : 'MGM+'},
        {value : 'Netflix', text : 'Netflix'},
        {value : 'Paramount+', text : 'Paramount+'},
        {value : 'PBS Masterpiece', text : 'PBS Masterpiece'},
        {value : 'Peacock', text : 'Peacock'},
        {value : 'Prime', text : 'Prime'},
        {value : 'Roku', text : 'Roku'},
        {value : 'Showtime', text : 'Showtime'},
        {value : 'Tubi', text : 'Tubi'}
    ],
    parentSelect : parentSelectData,
    currWatchingRepeat : _currWatchingData,
    upNextRepeat : _upNextData,
    currWatchingVisibile : _currWatchingVisibile,
    upNextVisibile : _upNextVisibile,
    allMedia : _allEntriesWithChildren
});

