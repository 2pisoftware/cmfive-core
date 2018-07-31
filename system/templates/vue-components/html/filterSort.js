function filterSort(descending, data, dataSort, dataFilter) {
	var sorter = natsort({ insensitive: true, desc: descending });

	return data.sort(function(a,b) {
		return sorter(a[dataSort], b[dataSort]);
	}).filter(function(row, index) {
		if (dataFilter) { 
			for (var key in dataFilter) { 
				if (dataFilter[key]) {
					return dataFilter[key].condition(row[key]);
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

/*var f = new Function('return ' + condition);
if (row[key].toLowerCase().indexOf(filter_value.toString().toLowerCase()) === -1) {
	return false;
}*/
