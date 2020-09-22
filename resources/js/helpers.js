import Api from '@/api';

var month = new Array();

month[0] = "January";
month[1] = "February";
month[2] = "March";
month[3] = "April";
month[4] = "May";
month[5] = "June";
month[6] = "July";
month[7] = "August";
month[8] = "September";
month[9] = "October";
month[10] = "November";
month[11] = "December";

var months = [];

months.push({ id: 1, name: 'January' });
months.push({ id: 2, name: 'February' });
months.push({ id: 3, name: 'March' });
months.push({ id: 4, name: 'April' });
months.push({ id: 5, name: 'May' });
months.push({ id: 6, name: 'June' });
months.push({ id: 7, name: 'July' });
months.push({ id: 8, name: 'August' });
months.push({ id: 9, name: 'September' });
months.push({ id: 10, name: 'October' });
months.push({ id: 11, name: 'November' });
months.push({ id: 12, name: 'December' });

window.Helper = {
    methods: {
        getUrlParams() {
            let regex = /[?&]([^=#]+)=([^&#]*)/g,
                url = window.location.href,
                params = {},
                match;

            while(match = regex.exec(url)) {
                params[match[1]] = match[2];
            }

            return params;
        },
        setObjectWithSameKeys(obj, o) {
            for (let key in obj) {
                obj[key] = o[key];
            }
        },
        setObjectValues(obj, val) {
            for (let key in obj) {
                obj[key] = val;
            }
        },
        checkIfObjectIsEmpty(obj) {
            for (let key in obj) {
                if (obj[key] != '') {
                    return false;
                }
            }
            return true;
        },
        isNullOrEmpty(value) {
            return (value === false || value === null || value === undefined || value === '' || value.length === 0);
        },
        handleError(error) {
            let retVal = null;

            if (error.response) {
                let data = error.response.data;

                if (data.http_status == 422) {
                    retVal = data.errors;

                    for (let key in retVal) {
                        retVal[key] = data.errors[key] ? data.errors[key][0] : '';
                    }

                } else {
                    window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
                    Bus.$emit('alertError', data.message);
                }

            } else {
                window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
                Bus.$emit('alertError', error);
            }

            return retVal;
        },
        getBearerAuth(record = null) {
            return record ? {
                headers: {
                    "Authorization" : "Bearer " + Api._getBearerToken()
                },
                data: record
            } : {
                headers: {
                    "Authorization" : "Bearer " + Api._getBearerToken()
                }
            };
        },
        formatPeriod(start, end) {
            let diff = (end === null) ? new Date() - new Date(start) : new Date(end) - new Date(start),
                offset = new Date().getTimezoneOffset()/60,
                hours = Math.abs(diff / 36e5) + offset,
                years, months, days, period = '';

            hours = Math.floor(hours);
            days = Math.floor(hours/24);

            if (days > 29) {
                months = Math.floor(days/30);

                if (months > 11) {
                    years = Math.floor(months/12);
                    period = (years == 1) ? '1 year' : years + ' years';

                    months = months - (years * 12);
                    // period += (months == 1) ? ' and 1 month' : '';
                    // period += (months != 1 && months > 0) ? ' and ' + months + ' months' : '';

                } else {
                    period += (months == 1) ? '1 month' : months + ' months';
                }

            } else {
                period = '1 month';
            }

            return period;
        },
        formatTimeDiffNow(created) {
            let diff = new Date(created) - new Date(),
                offset = new Date().getTimezoneOffset() / 60,
                hours = Math.abs(diff / 36e5) + offset,
                years, months, days, minutes, seconds;

            if (hours < 1) {
                minutes = Math.floor(hours*60);
                if (minutes < 1) {
                    seconds = Math.floor(hours*60*60);
                    return 'Just now';
                }
                return (minutes == 1) ? 'Posted 1 min ago' : 'Posted ' + minutes + ' mins ago';
            }

            hours = Math.floor(hours);
            if (hours > 23) {
                days = Math.floor(hours/24);

                if (days > 29) {
                    months = Math.floor(days/30);

                    if (months > 11) {
                        years = Math.floor(months/12);

                        return (years == 1) ? 'Posted a year ago' : 'Posted ' + years + ' years ago'
                    }

                    return (months == 1) ? 'Posted a month ago' : 'Posted ' + months + ' months ago';
                }

                return (days == 1) ? 'Posted 1 day ago' : 'Posted ' + days + ' days ago';
            }

            return (hours == 1) ? 'Posted 1 hr ago' : 'Posted ' + hours + ' hrs ago';
        },
        getInitials(name) {
            let initials = name.split(' ');

            return (initials.length > 1) ? initials[0].charAt(0).toUpperCase() + initials[1].charAt(0).toUpperCase() :
                                            initials[0].charAt(0).toUpperCase();
        },
        getDaysInMonth(month, year) {
           return new Date(year, month, 0).getDate();
        },
        getMonth(index) {
            return month[index];
        },
        getMonths() {
            return months;
        },
        getYears(startYear = new Date().getFullYear(), endYear = 1950) {
            let years = [];

            while ( startYear >= endYear ) {
                years.push(startYear--);
            }

            return years;
        },
        textAreaAdjust(o) {
            o.style.height = 'auto';
            o.style.height = (2 + o.scrollHeight) + 'px';
        },
        formatCheckbox(refs, input, refName, value) {

            if (value == 1) {
                refs[refName + '_1'].checked = true;
                refs[refName + '_0'].checked = false;

            } else if (value == 0) {
                refs[refName + '_1'].checked = false;
                refs[refName + '_0'].checked = true;

            } else {
                refs[refName + '_1'].checked = false;
                refs[refName + '_0'].checked = false;
            }

            if (input) {
                input[refName] = value;
            }
        },
        formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        formatCurrency(e, blur) {
            let value = e.target.value;

            if (value === '') return;

            let original_len = value.length,
                caret_pos = e.target.selectionStart,
                decimal_pos = value.indexOf(".");

            if (decimal_pos >= 0) {
                value = '$' + this.formatNumber(value.substring(0, decimal_pos)) +
                        '.' + (this.formatNumber(value.substring(decimal_pos)) +
                        (blur === 'blur' ? '00' : '')).substring(0, 2);

            } else {
                value = '$' + this.formatNumber(value);

                if (blur === 'blur') {
                    value += '.00';
                }
            }

            e.target.value = value;

            caret_pos = value.length - original_len + caret_pos;

            e.target.setSelectionRange(caret_pos, caret_pos);

            return e.target.value;
        },
        hashCode(str) {
            let hash = 0;

            for (let i = 0; i < str.length; i++) {
               hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }

            return hash;
        },
        intToRGB(i){
            let c = (i & 0x00FFFFFF)
                .toString(16)
                .toUpperCase();

            return "00000".substring(0, 6 - c.length) + c;
        },
        getColorHex(str) {
            return this.intToRGB(this.hashCode(str));
        },
        onFileChange(e, id = null, type = null) {
            let files = e.target.files || e.dataTransfer.files;

            if (! files.length) return;

            let file = files[0],
                reader  = new FileReader();

            reader.addEventListener('load', function () {
                Bus.$emit('imageToCrop', reader.result, id, type, file.type);
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        },
        getYearsJobRequirements(noOfYears) {

            if (!noOfYears) {

                noOfYears = 50;
            }

            return [...Array(noOfYears).keys()];
        },
        createDayTimeIncrements(hourBreakdownAmount) {
            let timeArray = [],
                hourInMins = 60,
                i, time;

            if(hourBreakdownAmount === 0)
                hourBreakdownAmount = 1;

            for( i = 0; i <= 24; i++) {
                if( i === 24 ) {
                    timeArray.push('24:00')
                } else {
                    for (time = 0; time < 60; time = time + (hourInMins / hourBreakdownAmount)) {
                        timeArray.push(this._setTimeStringItem(i) + ':' + this._setTimeStringItem(time));
                    }
                }
            }

            return timeArray;
        },
        _setTimeStringItem(number) {
            let stringNum = number.toString();

            if( stringNum.length === 1 )
                return '0' + stringNum
            else
                return stringNum
        }
    }
}
