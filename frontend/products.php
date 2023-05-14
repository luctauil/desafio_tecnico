<?php include 'header.php'; ?>

<style>
.even-row {
  background-color: #f2f2f2;
}

.odd-row {
  background-color: #ffffff;
}
</style>


<div id="app">
    <div class="container">
      <h1 class="text-center">Cadastro Produto</h1>

      <form id="productForm"  @submit.prevent="InsertProduct">
         <div class="form-group">
            <label for="productName">Produto:</label>
            <input type="text" class="form-control" id="productName" name="productName" v-model="productName" required>
         </div>


         <div class="form-group">
                <label for="price">Preço:</label>
                <input type="text" class="form-control" id="price" v-model="price" required>
        </div>

        <div class="form-group">
                <label for="tipoProduto">Tipo de Produto:</label>
                <select class="form-control" id="product_type" v-model="product_type" required>
                    <option v-for="productType in productTypes" :value="productType.id">{{ productType.name }}</option>
                </select>
            </div>

         <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>

      <hr>

      <h2>Produtos Cadastradas:</h2>
            <div class="row" id="productGrid">
            <div class="col-12">
                <ul class="list-group">
                <li class="list-group-item">
                    <div class="row odd-row">
                        <div class="col-3"><strong>Produto</strong></div>
                        <div class="col-3"><strong>Preço</strong></div>
                        <div class="col-3"><strong>Tipo de Produto</strong></div>
                    </div>
                </li>
                <li class="list-group-item" v-for="(product, index) in products" :key="product.id" :class="index % 2 === 0 ? 'even-row' : 'odd-row'">
                    <div class="row">
                        <div class="col-4">{{ product.name }}</div>
                        <div class="col-4">{{ product.price }}</div>
                        <div class="col-4">{{ product.type }}</div>
                    </div>
                </li>
                </ul>
            </div>
            </div>


</div>

</div>

    </div>
</div>

   <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

   <script>
      new Vue({
   el: '#app',
   data: {
        productName: '',
        product_type: '',
        productTypes: [],
        products: [],
        price: '',

    },
   methods: {
        InsertProduct() {

         axios.post('../backend/api.php?endpoint=product' , new URLSearchParams({
                        'productName': this.productName,
                        'product_type': this.product_type,
                        'price': this.price
                    }))
            .then(response => {
               this.productName = '';
               this.product_type = '';
               this.price = '';

               console.log(response.data);

               this.findProducts();
            })
            .catch(error => {
               console.error(error);
            });

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
        findTypeProduct() {
         axios.get('../backend/api.php?endpoint=product_type')
            .then(response => {
                console.log(response.data);

               this.productTypes = response.data;
            })
            .catch(error => {
               console.error(error);
            });
      }
   },
   mounted() {
        this.findTypeProduct();
        this.findProducts();
   }
});

   </script>


<?php include 'footer.php'; ?>
