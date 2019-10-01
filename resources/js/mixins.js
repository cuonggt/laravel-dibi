import numeral from 'numeral';

export default {
    methods: {
        formatNumber(number, format = '0,0') {
            return numeral(number).format(format);
        },
    },
};
