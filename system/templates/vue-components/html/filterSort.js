function filterSort(descending, data, dataSort, dataFilter) {
	var sorter = natsort({ insensitive: true, desc: descending });

	return data.sort(function(a,b) {
		return sorter(a[dataSort], b[dataSort]);
	}).filter(function(row, index) {
		if (dataFilter) {
			for (var key in dataFilter) {
				if (dataFilter[key] && dataFilter[key] !== "") {
					// if (row[key].toLowerCase() !== dataFilter[key].toString().toLowerCase())
					if (row[key].toLowerCase().indexOf(dataFilter[key].toString().toLowerCase()) === -1)
						return false;
				} 
			}
		}
		
		return true;
	});
}

function paginate(data, start, end) {
    return data.filter(function(row, index) {
        if (index >= start && index < end) 
            return true;
    });
}
