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
      <h1 class="text-center">Cadastro de Tipo de Produto</h1>

      <form id="productTypeForm"  @submit.prevent="InsertTypeProduct">
         <div class="form-group">
            <label for="productName">Nome do Tipo de Produto:</label>
            <input type="text" class="form-control" id="productName" name="productName" v-model="productName" required>
         </div>
         <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>

      <hr>

      <h2>Tipos de Produtos Cadastrados:</h2>
      <div class="row" id="productTypeGrid">
        <div class="col-12">
            <ul class="list-group">
            <li class="list-group-item" v-for="(productType, index) in productTypes" :key="productType.id" :class="index % 2 === 0 ? 'even-row' : 'odd-row'">
                {{ productType.name }}
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
      productTypes: []
   },
   methods: {
        InsertTypeProduct() {
         const formData = {
            productName: this.productName
         };

         axios.post('../backend/api.php?endpoint=product_type' , new URLSearchParams({
                        'productName': this.productName,
                    }))
            .then(response => {
               this.productName = '';

               console.log(response.data);

               this.findTypeProduct();
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
   }
});

   </script>


<?php include 'footer.php'; ?>
