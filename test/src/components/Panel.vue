<template>
  <b-container fluid>




    <b-navbar toggleable="lg" class="dashboard" style="background-color: rgba(0, 0, 0, 0.8);">
  <b-navbar-brand href="#" style="color: white;" id="tittle">Wardrobe App</b-navbar-brand>
  
  <!-- Navbar Toggle Button with PayPal Blue Color -->
  <b-navbar-toggle target="nav-collapse" style="background-color: #0070ba; border-color: #0070ba;"></b-navbar-toggle>

  <b-collapse id="nav-collapse" is-nav>
    <b-navbar-nav class="ml-auto nav">
      <b-nav-item v-if="userDetails" style="color: white;">
        <span style="color: white;">{{ userDetails.email }}</span>
      </b-nav-item>
      <b-nav-item @click="logout">
        <span style="color: white;">Logout</span>
      </b-nav-item>
    </b-navbar-nav>
  </b-collapse>
</b-navbar>





    <!-- Success & Error Messages -->
    <b-alert v-if="successMessage" variant="success" dismissible @dismissed="successMessage = ''">
      {{ successMessage }}
    </b-alert>
    <b-alert v-if="errorMessage" variant="danger" dismissible @dismissed="errorMessage = ''">
      {{ errorMessage }}
    </b-alert>

    <!-- Profile and Item Management Section -->
    <b-row class="mt-3">
      <!-- Profile Info -->
      <b-col md="4" sm="12">
        <b-card>
          <b-card-img :src="userDetails.profile_picture || '../assets/dp.jpg'" />

          <b-card-body>
            <h5 class="card-title header">{{ userDetails.email || 'Loading...' }}</h5>
            <p class="card-text txt">Manage your wardrobe items efficiently.</p>
          </b-card-body>
        </b-card>
      </b-col>

      <!-- Wardrobe Items -->
      <b-col md="8" sm="12">
        <b-card>
          <b-card-body>
            <b-button @click="toggleItemForm" variant="primary">Add New Item</b-button>

            <!-- Add Item Form -->
            <b-collapse v-model="isFormVisible">
              <b-form @submit.prevent="addItem" enctype="multipart/form-data">
                <b-form-group label="Item Name">
                  <b-form-input v-model="newItem.name" required></b-form-input>
                </b-form-group>
                <b-form-group label="Category">
                  <b-form-select v-model="newItem.category" :options="categories" required></b-form-select>
                </b-form-group>
                <b-form-group label="Description">
                  <b-form-textarea v-model="newItem.description" required></b-form-textarea>
                </b-form-group>
                <b-form-group label="Image">
                  <b-form-file v-model="newItem.photo" accept="image/*" placeholder="Choose an image or drop it here..." required></b-form-file>
                </b-form-group>
                <b-button type="submit" variant="primary">Add Item</b-button>
              </b-form>
            </b-collapse>

            <!-- Item List -->
            <b-list-group class="mt-3">
              <b-list-group-item v-for="(item, index) in filteredItems" :key="index">
                <b-row>
                  <b-col md="2" v-if="item.image_link">
                    <img :src="getImageUrl(item.image_link)" alt="Item Image" class="img-thumbnail" style="max-width: 80px;">
                  </b-col>
                  <b-col>
                    <strong>{{ item.name }}</strong> ({{ item.category }})
                    <p class="mb-0">{{ item.description }}</p>
                  </b-col>
                  <b-col class="text-right">
                    <b-button @click="likeItem(item.id)" variant="outline-primary" size="sm" class="mr-1 m-1">
                      <span v-if="item.favorite" style="color: red;">❤️</span>
                      <span v-else>🤍</span>
                    </b-button>
                    <b-button @click="editItem(item)" variant="primary" size="sm" class="mr-1 ">Edit</b-button>
                    <b-button @click="deleteItem(item.id)" variant="danger" size="sm" class="m-1">Delete</b-button>
                  </b-col>
                </b-row>
              </b-list-group-item>
            </b-list-group>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Search Bar -->
    <b-row class="mt-4 justify-content-center">
      <b-col md="6" sm="12">
        <b-form-input
          v-model="searchQuery"
          @input="searchItems"
          placeholder="Search cloth by name, category or description"
          class="form-control-lg"
        />
      </b-col>
    </b-row>

    <!-- Edit Modal -->
    <b-modal v-model="showEditModal" title="Edit Item" @ok="updateItem">
      <b-form>
        <b-form-group label="Item Name">
          <b-form-input v-model="editingItem.name"></b-form-input>
        </b-form-group>
        <b-form-group label="Category">
          <b-form-select v-model="editingItem.category" :options="categories"></b-form-select>
        </b-form-group>
        <b-form-group label="Description">
          <b-form-textarea v-model="editingItem.description"></b-form-textarea>
        </b-form-group>
        <b-form-group label="Image">
          <b-form-file v-model="editingItem.photo" accept="image/*" placeholder="Choose a new image..." />
        </b-form-group>
      </b-form>
    </b-modal>
  </b-container>
</template>

<script>
import {
  BNavbar, BNavbarBrand, BNavbarToggle, BCollapse, BNavItem, BNavbarNav, BContainer, BRow, BCol,
  BCard, BCardBody, BCardImg, BButton, BForm, BFormGroup, BFormInput, BFormSelect, BFormTextarea,
  BListGroup, BListGroupItem, BFormFile, BModal, BAlert
} from 'bootstrap-vue-next';
import axios from 'axios';
import dp from "../assets/dp.jpg";
//import { BAlert } from 'bootstrap-vue-next';

export default {
  components: {
    BNavbar, BNavbarBrand, BNavbarToggle, BCollapse, BNavItem, BNavbarNav, BContainer, BRow, BCol,
    BCard, BCardBody, BCardImg, BButton, BForm, BFormGroup, BFormInput, BFormSelect, BFormTextarea,
    BListGroup, BListGroupItem, BFormFile, BModal, BAlert
  },
  data() {
    return {
      //dpImage: require('../assets/dp.jpg') ,
      userDetails: {},
      categories: ['Tops', 'Bottoms', 'Shoes', 'Accessories'],
      items: [],
      filteredItems: [],
      successMessage: '',
      errorMessage: '',
      searchQuery: '',
      newItem: {
        name: '',
        category: '',
        description: '',
        photo: null
      },
      isFormVisible: false,
      showEditModal: false,
      editingItem: {
        id: null,
        name: '',
        category: '',
        description: '',
        photo: null
      },
      apiBaseUrl: 'http://localhost:8000/api'
    };
  },
  methods: {
    toggleItemForm() {
      this.isFormVisible = !this.isFormVisible;
    },

   showSuccessMessage(message) {
       this.successMessage = message;
      setTimeout(() => {
        this.successMessage = '';
      }, 4000);
    },

    showErrorMessage(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 4000);
    },

    getImageUrl(imageName) {
      return `http://localhost/realEstatePlatform/storage/app/public/photos/${imageName}`;
    },

    async getUserDetails() {
      try {
        const response = await axios.get(`${this.apiBaseUrl}/wardrob/info`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        if (response.data.success) {
          this.userDetails = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching user details:', error);
        if (error.response && error.response.status === 401) {
          this.logout();
        }
      }
    },

    async fetchItems() {
      try {
        const response = await axios.get(`${this.apiBaseUrl}/wardrob/cloth`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        if (response.data.success) {
          this.items = response.data.data;
          this.filteredItems = this.items;
        }
      } catch (error) {
        console.error('Error fetching items:', error);
        if (error.response && error.response.status === 401) {
          this.logout();
        }
      }
    },

    async addItem() {
      try {
        const formData = new FormData();
        formData.append('name', this.newItem.name);
        formData.append('category', this.newItem.category);
        formData.append('description', this.newItem.description);
        formData.append('photo', this.newItem.photo);

        const response = await axios.post(`${this.apiBaseUrl}/wardrob/upload`, formData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        if (response.data.success) {
          this.fetchItems();
          this.newItem = { name: '', category: '', description: '', photo: null };
          this.isFormVisible = false;
          this.showSuccessMessage("Item added successfully.");
        }
      } catch (error) {
        console.error('Error adding item:', error);
        this.showErrorMessage("Failed to add item.");
      }
    },

    async deleteItem(itemId) {
      try {
        const response = await axios.delete(`${this.apiBaseUrl}/wardrob/delete/${itemId}`, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.success) {
          this.fetchItems();
          this.showSuccessMessage("Item deleted successfully.");
        }
      } catch (error) {
        console.error('Error deleting item:', error);
        this.showErrorMessage("Failed to delete item.");
      }
    },

    async searchItems() {
      if (this.searchQuery.trim()) {
        this.filteredItems = this.items.filter(item =>
          item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.category.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          item.description.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      } else {
        this.filteredItems = this.items;
      }
    },

    editItem(item) {
      this.editingItem = {
        id: item.id,
        name: item.name,
        category: item.category,
        description: item.description,
        photo: null // Reset photo when editing
      };
      this.showEditModal = true;
    },

    async updateItem() {
      try {
        const formData = new FormData();
        formData.append('name', this.editingItem.name);
        formData.append('category', this.editingItem.category);
        formData.append('description', this.editingItem.description);
        if (this.editingItem.photo) {
          formData.append('photo', this.editingItem.photo);
        }

        const response = await axios.post(`${this.apiBaseUrl}/wardrob/update/${this.editingItem.id}`, formData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        if (response.data.success) {
          this.fetchItems();
          this.showSuccessMessage("Item updated successfully.");
        }
      } catch (error) {
        console.error('Error updating item:', error);
        this.showErrorMessage("Failed to update item.");
      }
    },

    async likeItem(itemId) {
      try {
        const response = await axios.post(`${this.apiBaseUrl}/wardrob/like/${itemId}`, {}, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (response.data.success) {
          this.fetchItems(); 
        }
      } catch (error) {
        console.error('Error liking item:', error);
      }
    },

    logout() {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
  },
  mounted() {
    const token = localStorage.getItem('token');
    if (!token) {
      this.logout();
      return;
    }
   
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    this.getUserDetails();
    this.fetchItems();
    // style="background-color: #3367d6";  #303f9f #0070ba;"
  }
};
</script>

<style scoped>
.card-body {
  background-color: #f8f9fa;
}
.header{
  font-size: 15px !important;
  font-weight: bold;
}

.txt{
  font-size: 10px !important;
}
.dashboard{
  color: #fff;
}
.form-control-lg {
  padding: 10px;
  font-size: 18px;
  border-radius: 20px;
}
.nav{
  color:#fff !important;
}
#tittle{
  color: white !important;
}

@media (max-width: 576px) {
  .form-control-lg {
    font-size: 16px;
  }
}
</style>






















