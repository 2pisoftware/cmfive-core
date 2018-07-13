function filterSort(descending, data, dataSort, dataFilter) {
	var sorter = natsort({ insensitive: true, desc: descending });

	return data.sort(function(a,b) {
		return sorter(a[dataSort], b[dataSort]);
	}).filter(function(row, index) {
		if (dataFilter) { 
			for (var key in dataFilter) { 
				if (dataFilter[key] && row[key] && row[key] != "") {
					var filter_value = null;
					
					if (dataFilter[key].hasOwnProperty("value")) {
						filter_value = dataFilter[key].value;
					} else {
						filter_value = dataFilter[key];
					}
					
					if (filter_value && filter_value != "") {
						var condition = "contains";

						if (dataFilter[key].hasOwnProperty("condition")) {
							condition = dataFilter[key].condition;
						}

						if (condition === "contains") {
							if (row[key].toLowerCase().indexOf(filter_value.toString().toLowerCase()) === -1) {
								return false;
							}
						} else if (condition === "===") {
							if (row[key].toLowerCase() !== filter_value.toString().toLowerCase()) {
								return false;
							}
						} else if (condition === ">= && <=") {
							if (Object.prototype.toString.call(filter_value) !== '[object Array]') return;
							if (filter_value.length < 2) return;

							if (filter_value[0] && filter_value[0] !== "") {
								if (filter_value[0] > row[key]) {
									return false;
								}
							}

							if (filter_value[1] && filter_value[1] !== "") {
								if (filter_value[1] < row[key]) {
									return false;
								}
							}
						}
					} 
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
