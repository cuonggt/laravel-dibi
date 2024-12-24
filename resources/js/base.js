import numeral from 'numeral';

export default {
    methods: {
        formatNumber(number, format = '0,0') {
            return numeral(number).format(format);
        },

        strLimit(value, limit = 55, end = '...') {
            if (value.length <= limit) {
                return value;
            }

            return `${value.substr(0, limit)}${end}`;
        },

        httpBuildQuery(params) {
            const query = new URLSearchParams();
            for (const [key, value] of Object.entries(params)) {
                query.append(key, value);
            }
            return query.toString();
        },
    },
};
