from django.urls import path
from . import views


urlpatterns = [
    path('', views.index),
    path('explore-text/', views.explore_text),
    path('explore-audio/', views.explore_audio),
    path('explore-video/', views.explore_video),
    path('about/', views.about)
]