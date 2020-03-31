
from rest_framework import generics

from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.forms import UserCreationForm, AuthenticationForm


# class LeadListCreate(generics.ListCreateAPIView):
#     queryset = Lead.objects.all()
#     serializer_class = LeadSerializer

# TODO: acct for additional information desired in model

def signup(request):
    if request.user.is_authenticated:
        return True
    if request.method == 'POST':
        form = UserCreationForm(request.POST)
        if form.is_valid():
            form.save()
            username = form.cleaned_data.get('username')
            password = form.cleaned_data.get('password1')
            user = authenticate(username=username, password=password)
            login(request, user)
            return True
        else:
            return False
    else:
        return False


def signin(request):
    if request.user.is_authenticated:
        return True
    if request.method == 'POST':
        username = request.POST['username']
        password = request.POST['password']
        user = authenticate(request, username=username, password=password)
        if user is not None:
            login(request, user)
            return True
        else:
            return False
    else:
        return False


def signout(request):
    logout(request)
    return True