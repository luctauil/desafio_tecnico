<?php include 'header.php'; ?>

<style>
.even-row {
  background-color: #f2f2f2;
}

.odd-row {
  background-color: #ffffff;
}
</style>

<div id="app" class="container">
        <h1 class="text-center">Taxa de Imposto por Tipo de Produto</h1>

        <form @submit.prevent="InsertTax">
            <div class="form-group">
                <label for="tipoProduto">Tipo de Produto:</label>
                <select class="form-control" id="product_type" v-model="product_type" required>
                    <option v-for="productType in productTypes" :value="productType.id">{{ productType.name }}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="taxaImposto">Taxa de Imposto:</label>
                <input type="text" class="form-control" id="tax" v-model="tax" required>
            </div>

            
            <div class="form-group">
                <br />
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>

            <hr>

            <h2>Taxas Cadastradas:</h2>
            <div class="row" id="productTypeGrid">
            <div class="col-12">
                <ul class="list-group">
                <li class="list-group-item" v-for="(taxe, index) in taxes" :key="taxe.id" :class="index % 2 === 0 ? 'even-row' : 'odd-row'">
                    <div class="row">
                        <div class="col-6">{{ taxe.name }}</div>
                        <div class="col-6">{{ taxe.tax_rate }}</div>
                    </div>
                </li>
                </ul>
            </div>
            </div>


        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                tax: '',
                product_type: '',
                productTypes: [], 
                taxes: [], 
            },
            methods: {
                InsertTax() {

                        axios.post('../backend/api.php?endpoint=tax' , new URLSearchParams({
                                        'product_type': this.product_type,
                                        'tax': this.tax,
                                    }))
                            .then(response => {
                                alert("Taxa cadastrada com sucesso!");
                                this.tax = '';
                                this.product_type = '';
                                this.loadTaxes();
                            })
                            .catch(error => {
                                console.error(error);
                            });

                            
                },
                findTypeProduct() {
                    axios.get('../backend/api.php?endpoint=product_type')
                        .then(response => {
                            this.productTypes = response.data;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                },
                loadTaxes() {
                    axios.get('../backend/api.php?endpoint=tax')
                        .then(response => {
                            console.log(response.data);
                            this.taxes = response.data;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            },
            mounted() {
                this.findTypeProduct();
                this.loadTaxes();
            }
        });
    </script>

<?php include 'footer.php'; ?>
