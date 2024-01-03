function createloaderDiv() {
	var div = document.createElement('div');
	div.className = 'loader';
	return div;
}

function getCurrentDate() {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth() + 1; //January is 0!
	var yyyy = today.getFullYear();

	if (dd < 10) {
		dd = '0' + dd
	}

	if (mm < 10) {
		mm = '0' + mm
	}

	today = yyyy + '-' + mm + '-' + dd;

	return today;
}

function isLeapYear(year) {
	return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
}

function get_last_12_month() {
	var date = new Date();

	var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
	var monthtotaldays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
	var range = {};

	var daystring = date.getDate();
	var monthstring = date.getMonth();
	var yearstring = date.getFullYear();


	var month = parseInt(monthstring) + 1;
	var day = parseInt(daystring);
	var year = parseInt(yearstring);

	var isleap = isLeapYear(year);

	if (day < 10) {
		daystring = "0" + day;
	}
	if (month < 10) {
		monthstring = "0" + (month);
	}

	var monthFirstDate = yearstring + "-" + monthstring + "-" + "01";
	// var monthLastDate = yearstring+"-"+monthstring+"-"+daystring;
	var monthLastDate = yearstring + "-" + monthstring + "-" + monthtotaldays[month];

	range[11] = {};
	range[11]["month"] = months[month - 1];
	range[11]["monthfirstdate"] = monthFirstDate;
	range[11]["monthlastdate"] = monthLastDate;

	for (var i = 10; i >= 0; i--) {
		month--;
		if (month == 0) {
			month = 12;
			year--;
		}
		//console.log(month);
		yearstring = year + "";

		if (month == 2) {
			monthstring = "0" + month;

			monthFirstDate = yearstring + "-" + monthstring + "-" + "01";
			if (isleap) {
				monthLastDate = yearstring + "-" + monthstring + "-" + 29;
			} else {
				monthLastDate = yearstring + "-" + monthstring + "-" + 28;
			}

		} else {
			if (month < 10) {
				monthstring = "0" + month;
			} else {
				monthstring = "" + month;
			}

			monthFirstDate = yearstring + "-" + monthstring + "-" + "01";
			monthLastDate = yearstring + "-" + monthstring + "-" + monthtotaldays[month - 1];
		}
		range[i] = {};
		range[i]["month"] = months[month - 1];
		range[i]["monthfirstdate"] = monthFirstDate;
		range[i]["monthlastdate"] = monthLastDate;
	}
	//console.log(range);
	return range;
}

function get_last_twelve_month() {
	const padZero = (value) => value < 10 ? `0${value}` : `${value}`;
	const dateToYYYYmmdd = (date) => [date.getFullYear(), padZero(date.getMonth() + 1), padZero(date.getDate())].join("-");
	const getFirstLastDateWithMonth = (date) => {
		return {
			month: date.toLocaleString('default', {
				month: 'short'
			}),
			monthfirstdate: dateToYYYYmmdd(new Date(date.getFullYear(), date.getMonth(), 1)),
			monthlastdate: dateToYYYYmmdd(new Date(date.getFullYear(), date.getMonth() + 1, 0))
		};
	};

	let lastTwelveMonth = [];
	for (let index = 0; index < 12; index++) {
		let d = new Date();
		d.setMonth(d.getMonth() - index);
		lastTwelveMonth.splice(0, 0, getFirstLastDateWithMonth(d));
	}

	return lastTwelveMonth;
}