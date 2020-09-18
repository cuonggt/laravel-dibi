import _ from 'lodash';

export default function fetchData(callback) {
    return function (to, from, next) {
        const promises = _.map(callback(to.params), (value, key) => {
            return new Promise((resolve, reject) => {
                (typeof value.then == 'function' ?
                    value :
                    axios.get(value).then(response => response.data))
                    .then(data => {
                        resolve({
                            key,
                            result: data,
                        });
                    });
            });
        });

        return Promise.all(promises)
            .then(response => {
                response.forEach(data => {
                    to.params[data.key] = data.result;
                });

                next();
            });
    }
}
