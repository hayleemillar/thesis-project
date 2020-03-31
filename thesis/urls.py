from django.urls import path
from . import views

urlpatterns = [
    path('signin/post/', views.signin),
    path('signup/post/', views.signup),
]