<template>
    <div class="container">
        <div class="card" v-if="count">
            <div class="card-header">
                <h5 class="card-title">Всего переходов по ссылке: {{count}}</h5>
            </div>
            <div class="card-body">
                <bar-chart
                    :chart-data = "data1"
                    :height     = "100"
                    :options    = "{
                        responsive: true,
                        maintainAspectRation: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    suggestedMin: 0
                                }
                            }]
                        }
                    }"
                ></bar-chart>
            </div>
        </div>
        <br>
        <div class="card"  v-if="count">
            <div class="card-header">
                <h5 class="card-title">Переходы по городам</h5>
            </div>
            <div class="card-body">
                <bar-chart
                        :chart-data = "data2"
                        :height     = "100"
                        :options    = "{
                        responsive: true,
                        maintainAspectRation: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    suggestedMin: 0
                                }
                            }]
                        }
                    }"
                ></bar-chart>
            </div>
        </div>
        <div class="card bg-info text-white"  v-else="!count">
            <div class="card-body">
                <p class="card-text text-center">Переходов по данной ссылке не осуществлялось</p>
            </div>
        </div>
    </div>
</template>

<script>
    import BarChart from './BarChart.js'

    export default {
        components: {
            BarChart
        },
        props: {
            idLink: String
        },
        data: function () {
            return {
                data1: [],
                data2: [],
                count: null
            }
        },
        mounted() {
            this.getData1For1Chart();
            this.getData2For1Chart();
            console.log('StatisticsComponent mounted.');
            // console.log(this.data);
        },
        methods: {
            getData1For1Chart: function () {
                axios.get('/data1', {
                    params: {id: this.idLink}
                }).then((response) => {
                    this.data1   = response.data.chart[0];
                    this.count  = response.data.count;
                }).catch(error => {
                    // here catch error messages from laravel validator and show them
                });
            },
            getData2For1Chart: function () {
                axios.get('/data2', {
                    params: {id: this.idLink}
                }).then((response) => {
                    this.data2   = response.data;
                }).catch(error => {
                    // here catch error messages from laravel validator and show them
                });
            }
        }
    }
</script>
