Templar.dataModel('Media', {
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
    a : [{a : 1, b : [1,2,3]},{a : 2, b : [4,5,6]}, {a : 3, b : [7,8,9]}],
    b : [
        {
          name : "hello",
          children : [{a : 1}, {a : 2}]
        },
        {
            name : "world",
            children : [{a : 3}, {a : 4}]
          }
      ],
      allMedia : _allEntriesWithChildren
});

