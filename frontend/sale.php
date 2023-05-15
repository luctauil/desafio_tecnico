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
    <h1 class="text-center">Cadastrar Venda</h1>

        <form id="saleForm"  @submit.prevent="InsertItem">
            <div class="form-group">
                <label for="product">Produto:</label>
                <select class="form-control" id="productSelected" v-model="productSelected" required>
                    <option v-for="product in products" :value="product.id">{{ product.name }} - {{ formatMoney( product.price ) }}</option>
                </select>
            </div>

         <div class="form-group">
                <label for="price">Quantidade:</label>
                <input type="text" class="form-control" id="qtd" v-model="qtd" required>
            </div>
        <br />
         <button type="submit" class="btn btn-primary">Adicionar</button>
      </form>
      <br />
            <h2>Itens da venda:</h2>
            <div class="row" id="productGrid">
            <div class="col-12">
                <ul class="list-group">
                <li class="list-group-item">
                    <div class="row odd-row">
                        <div class="col-2"><strong>Produto</strong></div>
                        <div class="col-2"><strong>Preço</strong></div>
                        <div class="col-2"><strong>Quantidade</strong></div>
                        <div class="col-2"><strong>Total Item</strong></div>
                        <div class="col-2"><strong>Impostos</strong></div>
                    </div>
                </li>
                <li class="list-group-item" v-for="(item, index) in itens_venda" :key="item.id" :class="index % 2 === 0 ? 'even-row' : 'odd-row'">
                    
                    <div class="row">
                        <div class="col-2">{{ item.name }}</div>
                        <div class="col-2">{{  formatMoney( item.price ) }}</div>
                        <div class="col-2">{{ item.qtd }}</div>
                        <div class="col-2">{{ formatMoney( item.total_price ) }}</div>
                        <div class="col-2">{{ formatMoney( item.tax_item ) }}</div>

                    </div>
                </li>
                </ul>
            </div>
            </div>
        <h4>Totais da venda:</h4>
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item">Total: <strong>{{ formatMoney( total_sale ) }}</strong></li>
                    <li class="list-group-item">Impostos: <strong>{{ formatMoney( total_tax ) }}</strong></li>
                </ul>
                <br />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form id="saleForm"  @submit.prevent="InsertSale">
                    <button type="submit" class="btn btn-success text-right">Efetivar venda</button>
                </form>
            </div>
        </div>
        <br />
        <hr />
        <br />
        <h4>Últimas Vendas:</h4>
        <div class="row">
            <div class="col-12">
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row odd-row">
                        <div class="col-2"><strong>Código</strong></div>
                        <div class="col-2"><strong>Data</strong></div>
                        <div class="col-2"><strong>Total da venda</strong></div>
                        <div class="col-2"><strong>Impostos</strong></div>
                    </div>
                </li>
                <li class="list-group-item" v-for="(sale, index) in sales" :key="sale.id" :class="index % 2 === 0 ? 'even-row' : 'odd-row'">
                    
                    <div class="row">
                        <div class="col-2">{{ sale.id }}</div>
                        <div class="col-2">{{ formatDateTime( sale.sale_date ) }}</div>
                        <div class="col-2">{{ formatMoney( sale.total_amount ) }}</div>
                        <div class="col-2">{{ formatMoney( sale.tax ) }}</div>
                    </div>
                </li>
                </ul>
                <br />
            </div>
        </div>
        <br />
        <hr />
        <br />


</div>
   <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

   <script>
      new Vue({
   el: '#app',
   data: {
        productSelected: '',
        products: [],
        qtd: '',
        itens_venda: [],
        total_sale: 0,
        total_tax: 0,
        sales: []
    },

   methods: {
    selectedProductPrice() {
                if (this.productSelected) {
                const selectedProduct = this.products.find(product => product.id === this.productSelected);
                return selectedProduct ? selectedProduct.price : null;
                }
                return null;
            },
            selectedProductTax() {
                if (this.productSelected) {
                const selectedProduct = this.products.find(product => product.id === this.productSelected);
                return selectedProduct ? selectedProduct.tax_rate : null;
                }
                return null;
            },
            selectedProductName() {
                if (this.productSelected) {
                const selectedProduct = this.products.find(product => product.id === this.productSelected);
                return selectedProduct ? selectedProduct.name : null;
                }
                return null;
            },
        findProducts() {
            axios.get('../backend/api.php?endpoint=product')
                        .then(response => {
                            this.products = response.data;
                        })
                        .catch(error => {
                            console.error(error);
                        });
        },
        findSales(){
            axios.get('../backend/api.php?endpoint=sale')
                        .then(response => {
                            this.sales = response.data;
                            console.log(this.sales);
                        })
                        .catch(error => {
                            console.error(error);
                        });
        },
        InsertItem() {
            const novoItem = {
                product_id: this.productSelected,
                name: this.selectedProductName(),
                price: this.selectedProductPrice(),
                qtd: this.qtd,
                total_price: (parseFloat(this.selectedProductPrice()) * parseFloat( this.qtd)),
                tax_rate: this.selectedProductTax(),
                tax_item: ((parseFloat(this.selectedProductTax()) * (parseFloat(this.selectedProductPrice()) * parseFloat( this.qtd))) / 100),
            };
            this.itens_venda.push(novoItem);
            this.qtd = '';
            console.log(this.itens_venda);
            this.UpdateTotal();
        },
        UpdateTotal()
        {
            let sum_itens = 0, sum_tax = 0;
            for (let i = 0; i < this.itens_venda.length; i++) {
                sum_itens += this.itens_venda[i].total_price;
                sum_tax += this.itens_venda[i].tax_item;
            }
            this.total_sale = sum_itens;
            this.total_tax = sum_tax;
        },

        InsertSale(){
            axios.post('../backend/api.php?endpoint=sale' , new URLSearchParams({
                        itens_venda: JSON.stringify(this.itens_venda),
                        total_sale: this.total_sale
                    }))
            .then(response => {
                alert("Venda cadastrada com sucesso!");
                this.itens_venda = [];
                this.qtd = '';
                this.total_sale = 0;
                this.total_tax = 0;
                this.findSales();
            })
            .catch(error => {
               console.error(error);
            });
            
        },
        formatMoney(value) {
            const numericValue = parseFloat(value);
            if (isNaN(numericValue)) {
            return '';
            }

            const options = {
            style: 'currency',
            currency: 'BRL'
            };
            return numericValue.toLocaleString('pt-BR', options);
        },
        formatDateTime(datetime) {
            const options = { day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' };
            return new Date(datetime).toLocaleDateString('pt-BR', options);
        }

   },
   mounted() {
        this.findProducts();
        this.findSales();
   },
   
   
});

   </script>


<?php include 'footer.php'; ?>
